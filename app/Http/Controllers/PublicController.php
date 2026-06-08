<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Message;
use App\Models\News;
use App\Models\Sanction;
use App\Models\User;

class PublicController extends Controller
{
    private const PUBLIC_SERVICES = [
        'layanan' => [
            'label' => 'Layanan',
            'description' => 'Pelanggaran siswa dan yang harus dilakukan sebagai tindak lanjut pembinaan.',
        ],
        'absensi' => [
            'label' => 'Absensi',
            'description' => 'Rekap hadir, izin, sakit, dan alfa untuk monitoring sekolah.',
        ],
        'lms' => [
            'label' => 'LMS',
            'description' => 'Materi dan kelas digital untuk proses belajar.',
        ],
        'komunikasi' => [
            'label' => 'Komunikasi',
            'description' => 'Pesan internal guru, siswa, dan orang tua.',
        ],
    ];

    public function home()
    {
        return view('public.home', [
            'featuredNews' => News::with('author')->latest('published_at')->take(3)->get(),
            'latestNews' => News::with('author')->latest('published_at')->skip(3)->take(6)->get(),
            'latestSanctions' => Sanction::with([
                'student.studentPoints' => function ($query) {
                    $query->where('type', 'pelanggaran')->latest('occurred_at')->latest('id');
                },
            ])
                ->latest()
                ->get()
                ->unique('student_id')
                ->values(),
            'stats' => [
                'students' => User::where('role', 'siswa')->count(),
                'teachers' => User::where('role', 'guru')->count(),
                'news' => News::count(),
            ],
        ]);
    }

    public function news()
    {
        return view('public.news-index', [
            'news' => News::with('author')->latest('published_at')->paginate(9),
        ]);
    }

    public function service(string $service)
    {
        abort_unless(array_key_exists($service, self::PUBLIC_SERVICES), 404);

        $data = [];

        if ($service === 'absensi') {
            $latestAttendances = Attendance::with('student')
                ->latest('date')
                ->latest('id')
                ->get()
                ->unique('student_id')
                ->values();

            $attendancesByStatus = $latestAttendances->groupBy('status');
            $data = [
                'attendanceSummary' => [
                    'hadir' => $attendancesByStatus->get('hadir', collect())->count(),
                    'izin' => $attendancesByStatus->get('izin', collect())->count(),
                    'sakit' => $attendancesByStatus->get('sakit', collect())->count(),
                    'alfa' => $attendancesByStatus->get('alfa', collect())->count(),
                ],
                'attendanceNames' => [
                    'hadir' => $attendancesByStatus->get('hadir', collect())->pluck('student.name')->filter()->values(),
                    'izin' => $attendancesByStatus->get('izin', collect())->pluck('student.name')->filter()->values(),
                    'sakit' => $attendancesByStatus->get('sakit', collect())->pluck('student.name')->filter()->values(),
                    'alfa' => $attendancesByStatus->get('alfa', collect())->pluck('student.name')->filter()->values(),
                ],
            ];
        }

        if ($service === 'layanan') {
            $data = [
                'latestSanctions' => Sanction::with([
                    'student.studentPoints' => function ($query) {
                        $query->where('type', 'pelanggaran')->latest('occurred_at')->latest('id');
                    },
                ])
                    ->latest()
                    ->get()
                    ->unique('student_id')
                    ->values(),
            ];
        }

        if ($service === 'lms') {
            $data = [
                'courses' => Course::latest()->take(12)->get(),
            ];
        }

        if ($service === 'komunikasi') {
            $data = [
                'messages' => Message::with(['sender', 'receiver'])->latest()->take(30)->get(),
            ];
        }

        return view('public.service-panel', array_merge($data, [
            'serviceKey' => $service,
            'serviceMeta' => self::PUBLIC_SERVICES[$service],
        ]));
    }

    public function newsShow(News $news)
    {
        return view('public.news-show', [
            'news' => $news->load('author'),
            'relatedNews' => News::whereKeyNot($news->id)->latest('published_at')->take(3)->get(),
        ]);
    }
}
