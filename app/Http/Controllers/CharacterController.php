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
        $students = User::where('role', 'siswa')->with(['parent'])->withSum('studentPoints as total_points', 'point')->orderBy('name')->get();

        if ($user->role === 'siswa') {
            $students = User::where('id', $user->id)->withSum('studentPoints as total_points', 'point')->get();
        }

        if ($user->role === 'orang_tua') {
            $students = $user->children()->withSum('studentPoints as total_points', 'point')->get();
        }

        return view('character.index', [
            'students' => $students,
            'points' => StudentPoint::with(['student', 'teacher'])->latest()->paginate(10),
            'sanctions' => Sanction::with('student')->latest()->take(10)->get(),
        ]);
    }

    public function create()
    {
        return view('character.create', [
            'students' => User::where('role', 'siswa')->orderBy('name')->get(),
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
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['prestasi', 'pelanggaran'])],
            'category' => ['required', Rule::in(['Disiplin', 'Tanggung Jawab', 'Kejujuran', 'Kerjasama'])],
            'point' => ['required', 'integer', 'between:-300,300'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['required', 'date'],
        ]);

        $data['teacher_id'] = $request->user()->id;
        $data['point'] = $data['type'] === 'pelanggaran' ? -abs($data['point']) : abs($data['point']);
        $service->recordPoint($data);

        return redirect()->route('character.index')->with('status', 'Data karakter siswa berhasil disimpan.');
    }

    public function report(User $student)
    {
        abort_unless($student->role === 'siswa', 404);
        $viewer = request()->user();

        abort_if($viewer->role === 'siswa' && $viewer->id !== $student->id, 403);
        abort_if($viewer->role === 'orang_tua' && $student->parent_id !== $viewer->id, 403);

        $student->load(['parent', 'studentPoints.teacher', 'sanctions']);
        $pdf = Pdf::loadView('character.report', [
            'student' => $student,
            'total' => $student->characterScore(),
        ])->setPaper('a4');

        return $pdf->download('laporan-karakter-'.$student->id.'.pdf');
    }
}
