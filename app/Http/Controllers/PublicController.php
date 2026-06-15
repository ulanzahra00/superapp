<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\LmsAssignment;
use App\Models\LmsSubmission;
use App\Models\Message;
use App\Models\News;
use App\Models\Sanction;
use App\Models\School;
use App\Models\User;

class PublicController extends Controller
{
    private const PUBLIC_SERVICES = [
        'layanan' => [
            'label' => 'Layanan',
            'description' => 'Akses ringkas layanan karakter siswa. Detail prestasi, pelanggaran, dan tindak lanjut hanya tersedia setelah masuk ke akun resmi sekolah.',
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
        if ($inactiveSchool = $this->inactiveSchoolFromRequest()) {
            return $this->maintenanceView($inactiveSchool);
        }

        $school = $this->publicSchool();
        $schoolQuery = $this->schoolQuery($school);

        return view('public.home', [
            'publicSchool' => $school,
            'schoolQuery' => $schoolQuery,
            'featuredNews' => News::with('author')->where('school_id', $school->id)->latest('published_at')->take(3)->get(),
            'latestNews' => News::with('author')->where('school_id', $school->id)->latest('published_at')->skip(3)->take(6)->get(),
            'stats' => [
                'students' => User::where('role', 'siswa')->forSchool($school->id)->count(),
                'teachers' => User::where('role', 'guru')->forSchool($school->id)->count(),
                'news' => News::where('school_id', $school->id)->count(),
            ],
        ]);
    }

    public function news()
    {
        if ($inactiveSchool = $this->inactiveSchoolFromRequest()) {
            return $this->maintenanceView($inactiveSchool);
        }

        $school = $this->publicSchool();
        $schoolQuery = $this->schoolQuery($school);

        return view('public.news-index', [
            'publicSchool' => $school,
            'schoolQuery' => $schoolQuery,
            'news' => News::with('author')->where('school_id', $school->id)->latest('published_at')->paginate(9)->withQueryString(),
        ]);
    }

    public function service(string $schoolSlugOrService, ?string $service = null)
    {
        $service = $service ?: $schoolSlugOrService;

        abort_unless(array_key_exists($service, self::PUBLIC_SERVICES), 404);

        if ($inactiveSchool = $this->inactiveSchoolFromRequest()) {
            return $this->maintenanceView($inactiveSchool);
        }

        $school = $this->publicSchool();
        $schoolQuery = $this->schoolQuery($school);
        $data = [];

        if ($service === 'absensi') {
            $latestAttendances = Attendance::with('student')
                ->where('school_id', $school->id)
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
            $search = trim((string) request('q'));
            $students = User::where('role', 'siswa')
                ->forSchool($school->id)
                ->withSum('studentPoints as total_points', 'point')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orderBy('name')
                ->paginate(9)
                ->withQueryString();

            $data = [
                'students' => $students,
                'search' => $search,
            ];
        }

        if ($service === 'lms') {
            $classSummaries = User::where('role', 'siswa')
                ->forSchool($school->id)
                ->whereNotNull('class_name')
                ->orderBy('class_name')
                ->get()
                ->groupBy('class_name')
                ->map(function ($students, $className) use ($school) {
                    return [
                        'name' => $className,
                        'students' => $students->count(),
                        'teachers' => User::where('role', 'guru')
                            ->forSchool($school->id)
                            ->where('class_name', $className)
                            ->orderBy('name')
                            ->pluck('name')
                            ->values(),
                        'assignments' => LmsAssignment::where('school_id', $school->id)
                            ->where('class_name', $className)
                            ->count(),
                    ];
                })
                ->values();

            $assignments = LmsAssignment::with('teacher')
                ->where('school_id', $school->id)
                ->latest()
                ->take(6)
                ->get();

            $courses = Course::where('school_id', $school->id)
                ->latest()
                ->get()
                ->unique(function ($course) {
                    return $course->name.'|'.$course->class_name;
                })
                ->values()
                ->take(8);

            $data = [
                'courses' => $courses,
                'lmsStats' => [
                    'classes' => $classSummaries->count(),
                    'teachers' => User::where('role', 'guru')->forSchool($school->id)->count(),
                    'students' => User::where('role', 'siswa')->forSchool($school->id)->count(),
                    'assignments' => LmsAssignment::where('school_id', $school->id)->count(),
                    'submissions' => LmsSubmission::where('school_id', $school->id)->count(),
                ],
                'classSummaries' => $classSummaries,
                'assignments' => $assignments,
            ];
        }

        if ($service === 'komunikasi') {
            $data = [
                'messages' => Message::with(['sender', 'receiver'])->where('school_id', $school->id)->latest()->take(30)->get(),
            ];
        }

        return view('public.service-panel', array_merge($data, [
            'publicSchool' => $school,
            'schoolQuery' => $schoolQuery,
            'serviceKey' => $service,
            'serviceMeta' => self::PUBLIC_SERVICES[$service],
        ]));
    }

    public function newsShow(News $news)
    {
        if ($inactiveSchool = $this->inactiveSchoolFromRequest()) {
            return $this->maintenanceView($inactiveSchool);
        }

        $school = $this->publicSchool();
        $schoolQuery = $this->schoolQuery($school);
        abort_unless($news->school_id === $school->id, 404);

        return view('public.news-show', [
            'publicSchool' => $school,
            'schoolQuery' => $schoolQuery,
            'news' => $news->load('author'),
            'relatedNews' => News::where('school_id', $school->id)->whereKeyNot($news->id)->latest('published_at')->take(3)->get(),
        ]);
    }

    private function publicSchool(): School
    {
        $slug = request()->route('schoolSlug') ?: request('school');

        if (! $slug) {
            if (auth()->check() && auth()->user()->school) {
                return auth()->user()->school;
            }

            $school = School::where('status', 'active')->orderBy('id')->first();

            if ($school) {
                return $school;
            }

            return School::updateOrCreate(
                ['slug' => 'sd-negeri-1-molinow'],
                [
                    'name' => 'SD Negeri 1 Molinow',
                    'status' => 'active',
                ]
            );
        }

        $school = $this->schoolFromSlug($slug, 'active');

        if ($school) {
            return $school;
        }

        abort_unless($school, 404);

        return $school;
    }

    private function schoolQuery(School $school): array
    {
        return ['schoolSlug' => $school->public_slug];
    }

    private function inactiveSchoolFromRequest(): ?School
    {
        $slug = request()->route('schoolSlug') ?: request('school');

        if (! $slug) {
            return null;
        }

        return $this->schoolFromSlug($slug, 'inactive');
    }

    private function schoolFromSlug(string $slug, string $status): ?School
    {
        $school = School::where('status', $status)->where('slug', $slug)->first();

        if ($school) {
            return $school;
        }

        $normalizedSlug = preg_replace('/[^a-z0-9]+/', '', strtolower($slug));

        return School::where('status', $status)->get()->first(function (School $school) use ($slug, $normalizedSlug) {
            return $school->public_slug === $slug
                || preg_replace('/[^a-z0-9]+/', '', strtolower($school->slug)) === $normalizedSlug;
        });
    }

    private function maintenanceView(School $school)
    {
        return response()->view('public.school-maintenance', [
            'publicSchool' => $school,
            'schoolQuery' => $this->schoolQuery($school),
        ], 503);
    }
}
