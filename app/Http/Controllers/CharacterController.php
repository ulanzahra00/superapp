<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use App\Models\StudentPoint;
use App\Models\User;
use App\Services\CharacterSanctionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CharacterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $students = $this->visibleStudentsQuery($user)
            ->with(['parent', 'studentPoints' => function ($query) {
                $query->latest('occurred_at')->latest();
            }])
            ->withCount([
                'studentPoints as violation_count' => function ($query) {
                    $query->where('type', 'pelanggaran');
                },
                'studentPoints as achievement_count' => function ($query) {
                    $query->where('type', 'prestasi');
                },
            ])
            ->withSum('studentPoints as total_points', 'point')
            ->orderBy('class_name')
            ->orderBy('name')
            ->get();

        if ($user->role === 'siswa') {
            $students = User::where('id', $user->id)->withSum('studentPoints as total_points', 'point')->get();
        }

        if ($user->role === 'orang_tua') {
            $students = $user->children()->withSum('studentPoints as total_points', 'point')->get();
        }

        $studentIds = $students->pluck('id');

        $sanctions = Sanction::with('student')
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->take(10)
            ->get();

        // Map sanksi per siswa untuk quick access di view
        $studentSanctions = $sanctions->groupBy('student_id');

        return view('character.index', [
            'students' => $students,
            'points' => StudentPoint::with(['student', 'teacher'])
                ->whereIn('student_id', $studentIds)
                ->latest()
                ->paginate(10),
            'sanctions' => $sanctions,
            'studentSanctions' => $studentSanctions,
        ]);
    }

    public function create(Request $request)
    {
        return view('character.create', [
            'students' => $this->visibleStudentsQuery($request->user())->orderBy('name')->get(),
            'templates' => [
                ['title' => 'Terlambat', 'type' => 'pelanggaran', 'point' => -5, 'category' => 'Disiplin'],
                ['title' => 'Tidak pakai seragam', 'type' => 'pelanggaran', 'point' => -10, 'category' => 'Disiplin'],
                ['title' => 'Bolos', 'type' => 'pelanggaran', 'point' => -20, 'category' => 'Tanggung Jawab'],
                ['title' => 'Juara lomba', 'type' => 'prestasi', 'point' => 20, 'category' => 'Kerjasama'],
                ['title' => 'Aktif kelas', 'type' => 'prestasi', 'point' => 10, 'category' => 'Tanggung Jawab'],
            ],
        ]);
    }

    public function store(Request $request, CharacterSanctionService $service)
    {
        $allowedStudentIds = $this->visibleStudentsQuery($request->user())->pluck('id')->all();

        $data = $request->validate([
            'student_id' => ['required', Rule::in($allowedStudentIds)],
            'type' => ['required', Rule::in(['prestasi', 'pelanggaran'])],
            'category' => ['required', Rule::in(['Disiplin', 'Tanggung Jawab', 'Kejujuran', 'Kerjasama'])],
            'point' => ['required', 'integer', 'between:-300,300'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['required', 'date'],
        ]);

        $data['teacher_id'] = $request->user()->id;
        $data['school_id'] = $request->user()->school_id;
        $data['point'] = $data['type'] === 'pelanggaran' ? -abs($data['point']) : abs($data['point']);
        $service->recordPoint($data);

        return redirect()->route('character.index')->with('status', 'Data karakter siswa berhasil disimpan.');
    }

    public function report(User $student)
    {
        abort_unless($student->role === 'siswa', 404);
        $viewer = request()->user();

        abort_unless($student->school_id === $viewer->school_id, 403);
        abort_if($viewer->role === 'siswa' && $viewer->id !== $student->id, 403);
        abort_if($viewer->role === 'orang_tua' && $student->parent_id !== $viewer->id, 403);
        abort_if($viewer->role === 'guru' && (! $viewer->class_name || $student->class_name !== $viewer->class_name), 403);

        $student->load(['parent', 'studentPoints.teacher', 'sanctions']);
        $pdf = Pdf::loadView('character.report', [
            'student' => $student,
            'total' => $student->characterScore(),
        ])->setPaper('a4');

        return $pdf->download('laporan-karakter-'.$student->id.'.pdf');
    }

    public function downloadSuratPanggilan(Sanction $sanction)
    {
        $user = request()->user();
        $student = $sanction->student;

        // Validasi akses
        abort_unless($student->school_id === $user->school_id, 403);
        abort_if($user->role === 'siswa' && $user->id !== $student->id, 403);
        abort_if($user->role === 'orang_tua' && $student->parent_id !== $user->id, 403);
        abort_if($user->role === 'guru' && (!$user->class_name || $student->class_name !== $user->class_name), 403);

        if (!$sanction->pdf_path) {
            abort(404, 'File PDF surat panggilan tidak ditemukan.');
        }

        $fullPath = storage_path('app/public/' . $sanction->pdf_path);
        if (!file_exists($fullPath)) {
            abort(404, 'File PDF surat panggilan tidak ditemukan.');
        }

        $filename = 'Surat_Panggilan_OrangTua_' . $student->name . '_' . date('Ymd') . '.pdf';
        return response()->download($fullPath, $filename);
    }

    private function visibleStudentsQuery(User $user)
    {
        $query = User::where('role', 'siswa')->forSchool($user->school_id);

        if ($user->role === 'guru') {
            if ($user->class_name) {
                $query->where('class_name', $user->class_name);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}
