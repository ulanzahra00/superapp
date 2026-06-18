<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Message;
use App\Models\News;
use App\Models\Sanction;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;
        $studentsQuery = User::where('role', 'siswa')->forSchool($schoolId);

        if ($user->role === 'guru') {
            if ($user->class_name) {
                $studentsQuery->where('class_name', $user->class_name);
            } else {
                $studentsQuery->whereRaw('1 = 0');
            }
        }

        $students = $studentsQuery
            ->with([
                'studentPoints' => function ($query) {
                    $query->with('teacher')->latest('occurred_at')->latest();
                },
                'sanctions' => function ($query) {
                    $query->latest();
                },
            ])
            ->withSum('studentPoints as total_points', 'point')
            ->orderBy('name')
            ->get();

        if ($user->role === 'siswa') {
            $students = collect([$user->load([
                'studentPoints' => function ($query) {
                    $query->with('teacher')->latest('occurred_at')->latest();
                },
                'sanctions' => function ($query) {
                    $query->latest();
                },
            ])->loadSum('studentPoints as total_points', 'point')]);
        }

        if ($user->role === 'orang_tua') {
            $students = $user->children()
                ->with([
                    'studentPoints' => function ($query) {
                        $query->with('teacher')->latest('occurred_at')->latest();
                    },
                    'sanctions' => function ($query) {
                        $query->latest();
                    },
                ])
                ->withSum('studentPoints as total_points', 'point')
                ->get();
        }

        $studentIds = $students->pluck('id');

        $stats = [
            'students' => $user->role === 'admin' ? User::where('role', 'siswa')->forSchool($schoolId)->count() : $students->count(),
            'teachers' => User::where('role', 'guru')->forSchool($schoolId)->count(),
            'attendance' => Attendance::where('school_id', $schoolId)->where('status', 'hadir')->count(),
            'violations' => StudentPoint::where('type', 'pelanggaran')
                ->where('school_id', $schoolId)
                ->when($user->role !== 'admin', function ($query) use ($studentIds) {
                    $query->whereIn('student_id', $studentIds);
                })
                ->count(),
            'sanctions' => Sanction::where('school_id', $schoolId)->when($user->role !== 'admin', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })->count(),
            'news' => News::where('school_id', $schoolId)->count(),
        ];

        $followUpStudents = $user->role === 'guru'
            ? $students->filter(function ($student) {
                return (int) ($student->total_points ?? 0) <= -20;
            })->values()
            : collect();

        $followedUpStudentIds = Sanction::whereIn('student_id', $studentIds)
            ->whereNotNull('followed_up_at')
            ->pluck('student_id')
            ->unique()
            ->toArray();

        return view('dashboard', [
            'stats' => $stats,
            'students' => $students,
            'followUpStudents' => $followUpStudents,
            'followedUpStudentIds' => $followedUpStudentIds,
            'recentPoints' => StudentPoint::with(['student', 'teacher'])
                ->where('school_id', $schoolId)
                ->when($user->role !== 'admin', function ($query) use ($studentIds) {
                    $query->whereIn('student_id', $studentIds);
                })
                ->latest()
                ->take(8)
                ->get(),
            'sanctions' => Sanction::with('student')
                ->where('school_id', $schoolId)
                ->when($user->role !== 'admin', function ($query) use ($studentIds) {
                    $query->whereIn('student_id', $studentIds);
                })
                ->latest()
                ->take(5)
                ->get(),
            'news' => News::with('author')->where('school_id', $schoolId)->latest('published_at')->take(4)->get(),
        ]);
    }

    public function respondFollowUp(Request $request, User $student)
    {
        $teacher = $request->user();

        abort_unless($teacher->hasRole('guru'), 403);
        abort_unless($student->school_id === $teacher->school_id, 403);
        abort_unless($student->hasRole('siswa') && $student->class_name === $teacher->class_name, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $score = (int) $student->studentPoints()->sum('point');
        abort_unless($score <= -20, 403);

        $message = 'Tindak lanjut wali kelas untuk '.$student->name.":\n\n".$data['body'];
        $receivers = collect([$student->id]);

        if ($student->parent_id) {
            $receivers->push($student->parent_id);
        }

        $receivers->unique()->each(function ($receiverId) use ($teacher, $message) {
            Message::create([
                'school_id' => $teacher->school_id,
                'sender_id' => $teacher->id,
                'receiver_id' => $receiverId,
                'category' => 'personal',
                'body' => $message,
                'read_at' => null,
            ]);
        });

        // Tandai sanksi terbaru sebagai sudah ditindak lanjuti
        $latestSanction = $student->sanctions()->latest()->first();
        if ($latestSanction) {
            $latestSanction->update(['followed_up_at' => now()]);
        }

        return redirect()->route('dashboard')->with('status', 'Respons tindak lanjut berhasil dikirim ke siswa'.($student->parent_id ? ' dan orang tua.' : '.'));
    }
}
