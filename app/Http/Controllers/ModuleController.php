<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\LmsAssignment;
use App\Models\LmsSubmission;
use App\Models\Message;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function attendance()
    {
        $schoolId = request()->user()->school_id;

        return view('modules.attendance', [
            'items' => Attendance::where('school_id', $schoolId)->latest('date')->take(20)->get(),
        ]);
    }

    public function lms()
    {
        $user = request()->user();

        if ($user && $user->hasRole('super_admin')) {
            return $this->superAdminLms();
        }

        $schoolId = $user->school_id;
        $courses = Course::where('school_id', $schoolId)->latest()->get()->unique(function ($course) {
            return $course->name.'|'.$course->class_name;
        })->values();

        if ($user && $user->hasRole('admin')) {
            $classes = User::where('role', 'siswa')
                ->forSchool($schoolId)
                ->whereNotNull('class_name')
                ->orderBy('class_name')
                ->get()
                ->groupBy('class_name')
                ->map(function ($students, $className) {
                    return [
                        'name' => $className,
                        'students' => $students->count(),
                        'teachers' => User::where('role', 'guru')
                            ->where('school_id', $students->first()->school_id)
                            ->where('class_name', $className)
                            ->orderBy('name')
                            ->pluck('name')
                            ->values(),
                        'assignments' => LmsAssignment::where('school_id', $students->first()->school_id)->where('class_name', $className)->count(),
                    ];
                })
                ->values();

            return view('modules.lms', [
                'courses' => $courses,
                'isAdminLms' => true,
                'isStudentLms' => false,
                'isTeacherLms' => false,
                'studentGrade' => null,
                'todayTasks' => collect(),
                'classStudentCount' => null,
                'assignments' => LmsAssignment::with('teacher')->where('school_id', $schoolId)->latest()->take(12)->get(),
                'lmsStats' => [
                    'classes' => $classes->count(),
                    'teachers' => User::where('role', 'guru')->forSchool($schoolId)->count(),
                    'students' => User::where('role', 'siswa')->forSchool($schoolId)->count(),
                    'parents' => User::where('role', 'orang_tua')->forSchool($schoolId)->count(),
                    'assignments' => LmsAssignment::where('school_id', $schoolId)->count(),
                ],
                'classSummaries' => $classes,
            ]);
        }

        if ($user && $user->hasRole('siswa')) {
            $grade = preg_match('/\d+/', (string) $user->class_name, $matches) ? (int) $matches[0] : null;
            $studentCourses = collect($this->elementaryLearningModules($grade));

            return view('modules.lms', [
                'courses' => $studentCourses,
                'isAdminLms' => false,
                'isStudentLms' => true,
                'isTeacherLms' => false,
                'studentGrade' => $grade,
                'todayTasks' => $this->elementaryDailyTasks($grade),
                'classStudentCount' => null,
                'assignments' => LmsAssignment::with('teacher')
                    ->where('school_id', $schoolId)
                    ->with(['submissions' => function ($query) use ($user) {
                        $query->where('student_id', $user->id);
                    }])
                    ->where('class_name', $user->class_name)
                    ->latest()
                    ->take(10)
                    ->get(),
            ]);
        }

        if ($user && $user->hasRole('guru')) {
            $grade = preg_match('/\d+/', (string) $user->class_name, $matches) ? (int) $matches[0] : null;

            return view('modules.lms', [
                'courses' => collect($this->elementaryLearningModules($grade)),
                'isAdminLms' => false,
                'isStudentLms' => false,
                'isTeacherLms' => true,
                'studentGrade' => $grade,
                'todayTasks' => $this->teacherDailyTasks(),
                'classStudentCount' => User::where('role', 'siswa')
                    ->forSchool($schoolId)
                    ->where('class_name', $user->class_name)
                    ->count(),
                'assignments' => LmsAssignment::where('teacher_id', $user->id)
                    ->where('school_id', $schoolId)
                    ->with(['submissions.student'])
                    ->latest()
                    ->take(10)
                    ->get(),
            ]);
        }

        return view('modules.lms', [
            'courses' => $courses,
            'isAdminLms' => false,
            'isStudentLms' => false,
            'isTeacherLms' => false,
            'studentGrade' => null,
            'todayTasks' => collect(),
            'classStudentCount' => null,
            'assignments' => collect(),
            'lmsStats' => [],
            'classSummaries' => collect(),
            'isSuperAdminLms' => false,
        ]);
    }

    private function superAdminLms()
    {
        $schools = School::orderByRaw("FIELD(status, 'active', 'pending', 'rejected')")
            ->orderBy('name')
            ->get();

        $schoolSummaries = $schools->map(function (School $school) {
            $classes = User::where('role', 'siswa')
                ->forSchool($school->id)
                ->whereNotNull('class_name')
                ->distinct()
                ->count('class_name');
            $assignments = LmsAssignment::where('school_id', $school->id);
            $lastAssignment = (clone $assignments)->latest()->first();

            return [
                'school' => $school,
                'classes' => $classes,
                'teachers' => User::where('role', 'guru')->forSchool($school->id)->count(),
                'students' => User::where('role', 'siswa')->forSchool($school->id)->count(),
                'parents' => User::where('role', 'orang_tua')->forSchool($school->id)->count(),
                'assignments' => (clone $assignments)->count(),
                'submissions' => LmsSubmission::where('school_id', $school->id)->count(),
                'last_assignment' => $lastAssignment,
            ];
        });

        $recentAssignments = LmsAssignment::with('teacher')
            ->latest()
            ->take(12)
            ->get();
        $schoolNames = $schools->pluck('name', 'id');

        return view('modules.lms', [
            'courses' => collect(),
            'isSuperAdminLms' => true,
            'isAdminLms' => false,
            'isStudentLms' => false,
            'isTeacherLms' => false,
            'studentGrade' => null,
            'todayTasks' => collect(),
            'classStudentCount' => null,
            'assignments' => $recentAssignments,
            'classSummaries' => collect(),
            'schoolSummaries' => $schoolSummaries,
            'schoolNames' => $schoolNames,
            'lmsStats' => [
                'active_schools' => $schools->where('status', 'active')->count(),
                'pending_schools' => $schools->where('status', 'pending')->count(),
                'classes' => $schoolSummaries->sum('classes'),
                'teachers' => $schoolSummaries->sum('teachers'),
                'students' => $schoolSummaries->sum('students'),
                'assignments' => LmsAssignment::count(),
                'submissions' => LmsSubmission::count(),
                'graded_submissions' => LmsSubmission::whereNotNull('graded_at')->count(),
            ],
        ]);
    }

    public function sendLmsAnnouncement(Request $request)
    {
        $admin = $request->user();
        abort_unless($admin->hasRole('admin'), 403);
        $schoolId = $admin->school_id;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:1500'],
        ]);

        $body = "Pengumuman LMS: {$data['title']}\n\n{$data['body']}";
        $receivers = User::whereIn('role', ['guru', 'siswa', 'orang_tua'])
            ->forSchool($schoolId)
            ->where('id', '!=', $admin->id)
            ->pluck('id');
        $sentAt = now();

        foreach ($receivers as $receiverId) {
            Message::create([
                'school_id' => $schoolId,
                'sender_id' => $admin->id,
                'receiver_id' => $receiverId,
                'category' => 'announcement',
                'body' => $body,
                'read_at' => null,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
        }

        return redirect()->route('lms')->with('status', 'Pengumuman LMS berhasil dikirim ke guru, siswa, dan orang tua.');
    }

    public function storeLmsAssignment(Request $request)
    {
        $teacher = $request->user();
        abort_unless($teacher->hasRole('guru'), 403);
        abort_unless((bool) $teacher->class_name, 403);
        $schoolId = $teacher->school_id;

        $data = $request->validate([
            'subject' => ['required', Rule::in(['Matematika', 'Bahasa Indonesia', 'IPAS', 'Pendidikan Karakter'])],
            'type' => ['required', Rule::in(['tugas', 'soal', 'refleksi'])],
            'title' => ['required', 'string', 'max:120'],
            'instructions' => ['required', 'string', 'max:1200'],
            'question' => ['nullable', 'string', 'max:1200'],
            'due_date' => ['nullable', 'date'],
        ]);

        $assignment = LmsAssignment::create([
            'school_id' => $schoolId,
            'teacher_id' => $teacher->id,
            'class_name' => $teacher->class_name,
            'subject' => $data['subject'],
            'type' => $data['type'],
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'question' => $data['question'] ?? null,
            'due_date' => $data['due_date'] ?? null,
        ]);

        $students = User::where('role', 'siswa')
            ->forSchool($schoolId)
            ->where('class_name', $teacher->class_name)
            ->get();

        foreach ($students as $student) {
            $body = "Tugas LMS baru: {$assignment->title}\n\nMata pelajaran: {$assignment->subject}\nInstruksi: {$assignment->instructions}";

            Message::create([
                'school_id' => $schoolId,
                'sender_id' => $teacher->id,
                'receiver_id' => $student->id,
                'category' => 'personal',
                'body' => $body,
                'read_at' => null,
            ]);

            if ($student->parent_id) {
                Message::create([
                    'school_id' => $schoolId,
                    'sender_id' => $teacher->id,
                    'receiver_id' => $student->parent_id,
                    'category' => 'personal',
                    'body' => $body,
                    'read_at' => null,
                ]);
            }
        }

        return redirect()->route('lms')->with('status', 'Tugas LMS berhasil dibuat dan muncul di LMS siswa.');
    }

    public function submitLmsAssignment(Request $request, LmsAssignment $assignment)
    {
        $student = $request->user();
        abort_unless($student->hasRole('siswa'), 403);
        abort_unless($assignment->school_id === $student->school_id, 403);
        abort_unless($assignment->class_name === $student->class_name, 403);

        $data = $request->validate([
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $submission = LmsSubmission::updateOrCreate(
            [
                'lms_assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'answer' => $data['answer'],
                'school_id' => $student->school_id,
                'score' => null,
                'feedback' => null,
                'submitted_at' => now(),
                'graded_at' => null,
            ]
        );

        Message::create([
            'school_id' => $student->school_id,
            'sender_id' => $student->id,
            'receiver_id' => $assignment->teacher_id,
            'category' => 'personal',
            'body' => "Jawaban LMS masuk dari {$student->name} untuk tugas {$assignment->title}. Silakan periksa di tab LMS.",
            'read_at' => null,
        ]);

        return redirect()->route('lms')->with('status', 'Jawaban berhasil dikirim ke guru.');
    }

    public function gradeLmsSubmission(Request $request, LmsSubmission $submission)
    {
        $teacher = $request->user();
        $submission->load(['assignment', 'student']);

        abort_unless($teacher->hasRole('guru'), 403);
        abort_unless($submission->school_id === $teacher->school_id, 403);
        abort_unless($submission->assignment->teacher_id === $teacher->id, 403);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:1500'],
        ]);

        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_at' => now(),
        ]);

        $course = Course::firstOrCreate(
            [
                'school_id' => $teacher->school_id,
                'teacher_id' => $teacher->id,
                'name' => $submission->assignment->subject,
                'class_name' => $submission->assignment->class_name,
            ],
            [
                'description' => 'Nilai tugas LMS '.$submission->assignment->subject,
            ]
        );

        Grade::updateOrCreate(
            [
                'student_id' => $submission->student_id,
                'course_id' => $course->id,
                'semester' => 'Ganjil',
            ],
            [
                'school_id' => $teacher->school_id,
                'score' => $data['score'],
            ]
        );

        $message = "Jawaban tugas LMS {$submission->assignment->title} sudah dinilai: {$data['score']}. Silakan lihat tab Nilai untuk rekap terbaru.";

        foreach (collect([$submission->student_id, $submission->student->parent_id])->filter()->unique() as $receiverId) {
            Message::create([
                'school_id' => $teacher->school_id,
                'sender_id' => $teacher->id,
                'receiver_id' => $receiverId,
                'category' => 'personal',
                'body' => $message,
                'read_at' => null,
            ]);
        }

        return redirect()->route('lms')->with('status', 'Jawaban berhasil dinilai dan nilai dikirim ke tab Nilai siswa.');
    }

    public function grades()
    {
        $user = request()->user();
        $schoolId = $user->school_id;
        $subjects = ['Matematika', 'Bahasa Indonesia', 'IPAS', 'Pendidikan Karakter'];
        $students = collect();
        $gradesQuery = Grade::with(['student', 'course'])->where('school_id', $schoolId)->latest();

        if ($user->hasRole('guru')) {
            $students = User::where('role', 'siswa')
                ->forSchool($schoolId)
                ->where('class_name', $user->class_name)
                ->with(['studentPoints'])
                ->withAvg('grades as average_score', 'score')
                ->orderBy('name')
                ->get();

            $gradesQuery->whereIn('student_id', $students->pluck('id'));
        } elseif ($user->hasRole('siswa')) {
            $students = collect([$user]);
            $gradesQuery->where('student_id', $user->id);
        } elseif ($user->hasRole('orang_tua')) {
            $students = $user->children()
                ->withAvg('grades as average_score', 'score')
                ->orderBy('name')
                ->get();
            $gradesQuery->whereIn('student_id', $students->pluck('id'));
        } else {
            $students = User::where('role', 'siswa')
                ->forSchool($schoolId)
                ->withAvg('grades as average_score', 'score')
                ->orderBy('class_name')
                ->orderBy('name')
                ->get();
        }

        $grades = $gradesQuery->take($user->hasRole('admin') ? 200 : 80)->get();
        $average = round((float) $grades->avg('score'), 1);

        return view('modules.grades', [
            'grades' => $grades,
            'students' => $students,
            'subjects' => $subjects,
            'average' => $average,
            'gradeStats' => [
                'students' => $students->count(),
                'grades' => $grades->count(),
                'average' => $average,
                'needs_support' => $grades->where('score', '<', 70)->count(),
            ],
        ]);
    }

    public function storeGrade(Request $request)
    {
        $teacher = $request->user();
        abort_unless($teacher->hasRole('guru'), 403);
        $schoolId = $teacher->school_id;

        $studentIds = User::where('role', 'siswa')
            ->forSchool($schoolId)
            ->where('class_name', $teacher->class_name)
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'student_id' => ['required', Rule::in($studentIds)],
            'subject' => ['required', Rule::in(['Matematika', 'Bahasa Indonesia', 'IPAS', 'Pendidikan Karakter'])],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ]);

        $course = Course::firstOrCreate(
            [
                'school_id' => $schoolId,
                'teacher_id' => $teacher->id,
                'name' => $data['subject'],
                'class_name' => $teacher->class_name,
            ],
            [
                'description' => 'Penilaian '.$data['subject'].' '.$teacher->class_name,
            ]
        );

        Grade::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'course_id' => $course->id,
                'semester' => $data['semester'],
            ],
            [
                'school_id' => $schoolId,
                'score' => $data['score'],
            ]
        );

        $student = User::find($data['student_id']);
        $message = "Nilai {$data['subject']} semester {$data['semester']} sudah diperbarui: {$data['score']}. Silakan lihat tab Nilai untuk rekap lengkap.";

        foreach (collect([$student->id, $student->parent_id])->filter()->unique() as $receiverId) {
            Message::create([
                'school_id' => $schoolId,
                'sender_id' => $teacher->id,
                'receiver_id' => $receiverId,
                'category' => 'personal',
                'body' => $message,
                'read_at' => null,
            ]);
        }

        return redirect()->route('grades')->with('status', 'Nilai siswa berhasil disimpan dan pemberitahuan dikirim.');
    }

    public function communication(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        Message::where('receiver_id', $user->id)
            ->where('school_id', $schoolId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::with(['sender', 'receiver'])
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->latest()
            ->take($user->hasRole('admin') ? 300 : 30)
            ->get();

        if ($user->hasRole('admin')) {
            $messages = $this->summarizeSentAnnouncements($messages, $user);
        }

        return view('modules.communication', [
            'recipients' => $this->availableMessageRecipients($user)->get(),
            'messages' => $messages->take(30)->values(),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole(['admin', 'guru']), 403);

        if ($user->hasRole('admin') && $request->input('receiver_id') === 'announcement') {
            $data = $request->validate([
                'receiver_id' => ['required', 'in:announcement'],
                'body' => ['required', 'string', 'max:2000'],
            ]);

            $receivers = User::whereIn('role', ['guru', 'siswa', 'orang_tua'])
                ->forSchool($user->school_id)
                ->where('id', '!=', $user->id)
                ->pluck('id');
            $sentAt = now();

            foreach ($receivers as $receiverId) {
                Message::create([
                    'school_id' => $user->school_id,
                    'sender_id' => $user->id,
                    'receiver_id' => $receiverId,
                    'category' => 'announcement',
                    'body' => $data['body'],
                    'read_at' => null,
                    'created_at' => $sentAt,
                    'updated_at' => $sentAt,
                ]);
            }

            return redirect()->route('communication')->with('status', 'Pengumuman berhasil dikirim ke guru, siswa, dan orang tua.');
        }

        $allowedReceiverIds = $this->availableMessageRecipients($user)->pluck('id')->all();

        $data = $request->validate([
            'receiver_id' => ['required', Rule::in($allowedReceiverIds)],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'school_id' => $user->school_id,
            'sender_id' => $user->id,
            'receiver_id' => $data['receiver_id'],
            'category' => 'personal',
            'body' => $data['body'],
            'read_at' => null,
        ]);

        return redirect()->route('communication')->with('status', 'Pesan berhasil dikirim.');
    }

    public function replyMessage(Request $request, Message $message)
    {
        $user = $request->user();
        abort_unless($message->school_id === $user->school_id, 403);
        abort_unless($message->sender_id === $user->id || $message->receiver_id === $user->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $receiverId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;

        Message::create([
            'school_id' => $user->school_id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'category' => 'personal',
            'body' => $data['body'],
            'read_at' => null,
        ]);

        if ($message->receiver_id === $user->id && ! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return redirect()->route('communication')->with('status', 'Balasan berhasil dikirim.');
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['children', 'studentPoints', 'sanctions']);

        return view('profile', compact('user'));
    }

    private function availableMessageRecipients(User $user)
    {
        if ($user->hasRole('admin')) {
            return User::where('id', '!=', $user->id)
                ->forSchool($user->school_id)
                ->whereIn('role', ['guru', 'siswa', 'orang_tua'])
                ->orderBy('role')
                ->orderBy('class_name')
                ->orderBy('name');
        }

        if ($user->hasRole('guru')) {
            return User::where('role', 'siswa')
                ->forSchool($user->school_id)
                ->where('class_name', $user->class_name)
                ->orderBy('name');
        }

        return User::whereRaw('1 = 0');
    }

    private function summarizeSentAnnouncements($messages, User $user)
    {
        $seen = [];

        return $messages->filter(function (Message $message) use (&$seen, $messages, $user) {
            if (($message->category ?? 'personal') !== 'announcement' || $message->sender_id !== $user->id) {
                return true;
            }

            $key = md5($message->body).'|'.$message->created_at->format('Y-m-d H:i:s');

            if (isset($seen[$key])) {
                return false;
            }

            $message->announcement_recipient_count = $messages->filter(function (Message $item) use ($message, $user) {
                return ($item->category ?? 'personal') === 'announcement'
                    && $item->sender_id === $user->id
                    && $item->body === $message->body
                    && $item->created_at->format('Y-m-d H:i:s') === $message->created_at->format('Y-m-d H:i:s');
            })->count();

            $seen[$key] = true;

            return true;
        })->values();
    }

    private function elementaryLearningModules(?int $grade)
    {
        $level = $grade && $grade <= 3 ? 'awal' : 'lanjut';

        return [
            [
                'name' => 'Matematika',
                'class_name' => 'Kelas '.($grade ?: 'SD'),
                'description' => $level === 'awal'
                    ? 'Berlatih penjumlahan, pengurangan, dan membaca pola bilangan.'
                    : 'Berlatih pecahan, perkalian, pembagian, dan soal cerita sederhana.',
                'duration' => '20 menit',
                'activity' => 'Kerjakan 5 soal latihan, lalu cocokkan jawaban bersama guru atau orang tua.',
                'tag' => 'Latihan angka',
            ],
            [
                'name' => 'Bahasa Indonesia',
                'class_name' => 'Kelas '.($grade ?: 'SD'),
                'description' => $level === 'awal'
                    ? 'Membaca cerita pendek dan menemukan tokoh utama.'
                    : 'Membaca teks pendek, mencari ide pokok, dan menulis ringkasan.',
                'duration' => '15 menit',
                'activity' => 'Baca satu cerita pendek, lalu tulis tiga kalimat tentang isi bacaan.',
                'tag' => 'Literasi',
            ],
            [
                'name' => 'IPAS',
                'class_name' => 'Kelas '.($grade ?: 'SD'),
                'description' => $level === 'awal'
                    ? 'Mengenal bagian tubuh, hewan, tumbuhan, dan lingkungan sekitar.'
                    : 'Mengamati energi, perubahan wujud benda, dan kebiasaan menjaga lingkungan.',
                'duration' => '15 menit',
                'activity' => 'Amati benda di sekitar rumah, lalu catat dua hal yang kamu temukan.',
                'tag' => 'Pengamatan',
            ],
            [
                'name' => 'Pendidikan Karakter',
                'class_name' => 'Kelas '.($grade ?: 'SD'),
                'description' => 'Membiasakan disiplin, tanggung jawab, sopan santun, dan berani bertanya.',
                'duration' => '10 menit',
                'activity' => 'Pilih satu kebiasaan baik hari ini dan ceritakan kepada wali kelas.',
                'tag' => 'Pembiasaan',
            ],
        ];
    }

    private function elementaryDailyTasks(?int $grade)
    {
        return collect([
            'Membaca materi selama 10 menit.',
            'Mengerjakan latihan singkat sesuai mata pelajaran.',
            'Menulis satu hal yang sudah dipahami hari ini.',
        ]);
    }

    private function teacherDailyTasks()
    {
        return collect([
            'Cek kesiapan materi dan aktivitas singkat sebelum pelajaran.',
            'Pantau siswa yang belum menyelesaikan latihan atau butuh bantuan.',
            'Tulis catatan tindak lanjut untuk pembelajaran berikutnya.',
        ]);
    }
}
