<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
        $studentsQuery = User::where('role', 'siswa');
        $students = $studentsQuery->withSum('studentPoints as total_points', 'point')->orderBy('name')->get();

        if ($user->role === 'siswa') {
            $students = collect([$user->loadSum('studentPoints as total_points', 'point')]);
        }

        if ($user->role === 'orang_tua') {
            $students = $user->children()->withSum('studentPoints as total_points', 'point')->get();
        }

        $stats = [
            'students' => User::where('role', 'siswa')->count(),
            'teachers' => User::where('role', 'guru')->count(),
            'attendance' => Attendance::where('status', 'hadir')->count(),
            'violations' => StudentPoint::where('type', 'pelanggaran')->count(),
            'sanctions' => Sanction::count(),
            'news' => News::count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'students' => $students,
            'recentPoints' => StudentPoint::with(['student', 'teacher'])->latest()->take(8)->get(),
            'sanctions' => Sanction::with('student')->latest()->take(5)->get(),
            'news' => News::with('author')->latest('published_at')->take(4)->get(),
        ]);
    }
}
