<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Message;
use App\Models\News;
use App\Models\Sanction;
use App\Models\School;
use App\Models\SchoolNotification;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $school = School::firstOrCreate(
            ['slug' => 'sd-negeri-1-molinow'],
            [
                'name' => 'SD Negeri 1 Molinow',
                'status' => 'active',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@sekolah.test'],
            [
                'school_id' => $school->id,
                'name' => 'Kepala Sekolah',
                'role' => 'admin',
                'phone' => '0811000001',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@sekolah.test'],
            [
                'school_id' => $school->id,
                'name' => 'Super Admin Pusat',
                'role' => 'super_admin',
                'phone' => '0811000000',
                'password' => Hash::make('password'),
            ]
        );

        $teacher = User::updateOrCreate(
            ['email' => 'guru@sekolah.test'],
            [
                'school_id' => $school->id,
                'name' => 'Ibu Guru Maya',
                'role' => 'guru',
                'class_name' => 'Kelas 4 A',
                'phone' => '0811000002',
                'password' => Hash::make('password'),
            ]
        );

        $parent = User::updateOrCreate(
            ['email' => 'ortu@sekolah.test'],
            [
                'school_id' => $school->id,
                'name' => 'Bapak Andi',
                'role' => 'orang_tua',
                'phone' => '0811000003',
                'password' => Hash::make('password'),
            ]
        );

        $studentSeeds = [
            ['Andi Pratama', 'siswa@sekolah.test', 'SIS001', 'Kelas 1 A', $parent->id],
            ['Siti Rahma', 'siti@sekolah.test', 'SIS002', 'Kelas 1 B', null],
            ['Budi Santoso', 'budi@sekolah.test', 'SIS003', 'Kelas 2 A', null],
            ['Dewi Lestari', 'dewi@sekolah.test', 'SIS004', 'Kelas 2 B', null],
            ['Rizky Maulana', 'rizky@sekolah.test', 'SIS005', 'Kelas 3 A', null],
            ['Nadia Putri', 'nadia@sekolah.test', 'SIS006', 'Kelas 3 B', null],
            ['Fajar Nugroho', 'fajar@sekolah.test', 'SIS007', 'Kelas 4 A', null],
            ['Ayu Wulandari', 'ayu@sekolah.test', 'SIS008', 'Kelas 4 B', null],
            ['Raka Saputra', 'raka@sekolah.test', 'SIS009', 'Kelas 5 A', null],
            ['Maya Permata', 'maya@sekolah.test', 'SIS010', 'Kelas 5 B', null],
            ['Dimas Firmansyah', 'dimas@sekolah.test', 'SIS011', 'Kelas 6 A', null],
            ['Intan Maharani', 'intan@sekolah.test', 'SIS012', 'Kelas 6 B', null],
            ['Bagas Aditya', 'bagas@sekolah.test', 'SIS013', 'Kelas 1 A', null],
            ['Citra Anggraini', 'citra@sekolah.test', 'SIS014', 'Kelas 2 A', null],
            ['Galih Prakoso', 'galih@sekolah.test', 'SIS015', 'Kelas 3 A', null],
            ['Laras Puspita', 'laras@sekolah.test', 'SIS016', 'Kelas 4 A', null],
            ['Yoga Ramadhan', 'yoga@sekolah.test', 'SIS017', 'Kelas 5 A', null],
            ['Putri Amelia', 'putri@sekolah.test', 'SIS018', 'Kelas 6 A', null],
            ['Arif Hidayat', 'arif@sekolah.test', 'SIS019', 'Kelas 5 B', null],
            ['Sekar Kinanti', 'sekar@sekolah.test', 'SIS020', 'Kelas 6 B', null],
        ];

        $students = collect($studentSeeds)->map(function ($item) use ($school) {
            [$name, $email, $nis, $className, $parentId] = $item;

            return User::updateOrCreate(
                ['email' => $email],
                [
                    'school_id' => $school->id,
                    'name' => $name,
                    'role' => 'siswa',
                    'parent_id' => $parentId,
                    'nis' => $nis,
                    'class_name' => $className,
                    'password' => Hash::make('password'),
                ]
            );
        });

        $cleanStudentSeeds = [
            ['Aldi Kurniawan', 'aldi@sekolah.test', 'SIS021', 'Kelas 1 B'],
            ['Nabila Safitri', 'nabila@sekolah.test', 'SIS022', 'Kelas 2 B'],
            ['Rehan Maulana', 'rehan@sekolah.test', 'SIS023', 'Kelas 3 B'],
            ['Kirani Azzahra', 'kirani@sekolah.test', 'SIS024', 'Kelas 4 B'],
            ['Farhan Ramli', 'farhan@sekolah.test', 'SIS025', 'Kelas 5 A'],
            ['Zahra Nuraini', 'zahra.nuraini@sekolah.test', 'SIS026', 'Kelas 5 B'],
            ['Rafi Alfarizi', 'rafi@sekolah.test', 'SIS027', 'Kelas 6 A'],
            ['Anisa Fitriani', 'anisa@sekolah.test', 'SIS028', 'Kelas 6 B'],
            ['Mika Prameswari', 'mika@sekolah.test', 'SIS029', 'Kelas 3 A'],
            ['Rangga Saputra', 'rangga@sekolah.test', 'SIS030', 'Kelas 4 A'],
        ];

        $cleanStudents = collect($cleanStudentSeeds)->map(function ($item) use ($school) {
            [$name, $email, $nis, $className] = $item;

            return User::updateOrCreate(
                ['email' => $email],
                [
                    'school_id' => $school->id,
                    'name' => $name,
                    'role' => 'siswa',
                    'parent_id' => null,
                    'nis' => $nis,
                    'class_name' => $className,
                    'password' => Hash::make('password'),
                ]
            );
        });

        $homeroomClasses = [
            'Kelas 1 A',
            'Kelas 1 B',
            'Kelas 2 A',
            'Kelas 2 B',
            'Kelas 3 A',
            'Kelas 3 B',
            'Kelas 4 A',
            'Kelas 4 B',
            'Kelas 5 A',
            'Kelas 5 B',
            'Kelas 6 A',
            'Kelas 6 B',
        ];

        foreach ($homeroomClasses as $className) {
            if (User::where('role', 'guru')->where('school_id', $school->id)->where('class_name', $className)->exists()) {
                continue;
            }

            User::updateOrCreate(
                ['email' => 'guru.'.Str::slug($className). '@sekolah.test'],
                [
                    'school_id' => $school->id,
                    'name' => 'Wali '.$className,
                    'role' => 'guru',
                    'class_name' => $className,
                    'phone' => null,
                    'password' => Hash::make('password'),
                ]
            );
        }

        $student = $students[0];
        $studentTwo = $students[1];

        StudentPoint::firstOrCreate(
            ['student_id' => $student->id, 'title' => 'Bolos', 'occurred_at' => now()->subDays(2)->toDateString()],
            [
                'teacher_id' => $teacher->id,
                'type' => 'pelanggaran',
                'category' => 'Disiplin',
                'point' => -20,
                'description' => 'Tidak hadir pada jam pelajaran tanpa keterangan.',
            ]
        );

        StudentPoint::firstOrCreate(
            ['student_id' => $student->id, 'title' => 'Aktif kelas', 'occurred_at' => now()->subDay()->toDateString()],
            [
                'teacher_id' => $teacher->id,
                'type' => 'prestasi',
                'category' => 'Tanggung Jawab',
                'point' => 10,
                'description' => 'Aktif membantu diskusi kelompok.',
            ]
        );

        StudentPoint::firstOrCreate(
            ['student_id' => $studentTwo->id, 'title' => 'Juara lomba', 'occurred_at' => now()->subDays(5)->toDateString()],
            [
                'teacher_id' => $teacher->id,
                'type' => 'prestasi',
                'category' => 'Kerjasama',
                'point' => 20,
                'description' => 'Juara lomba sains tingkat kota.',
            ]
        );

        Sanction::updateOrCreate(
            ['student_id' => $student->id, 'sanction_type' => 'Peringatan 1'],
            [
                'total_points' => -20,
                'note' => 'Sanksi awal dari data contoh.',
            ]
        );

        SchoolNotification::firstOrCreate(
            ['user_id' => $parent->id, 'title' => 'Pelanggaran siswa', 'message' => 'Andi Pratama mendapat -20 poin: Bolos.'],
            ['level' => 'warning']
        );

        $courses = collect([
            ['Matematika', 'Materi fungsi, turunan, dan latihan adaptif.'],
            ['Bahasa Indonesia', 'Literasi, teks argumentasi, dan presentasi.'],
            ['Bahasa Inggris', 'Reading, speaking, dan writing project.'],
            ['Fisika', 'Gerak, energi, dan praktikum sederhana.'],
            ['Sejarah', 'Kajian sejarah Indonesia dan diskusi kelas.'],
        ])->map(function ($item) use ($teacher) {
            [$name, $description] = $item;

            return Course::create([
                'teacher_id' => $teacher->id,
                'name' => $name,
                'class_name' => 'Lintas Kelas',
                'description' => $description,
            ]);
        });

        $statuses = ['hadir', 'izin', 'sakit', 'alfa'];
        $communicationTopics = [
            'hadir' => 'Terima kasih sudah hadir tepat waktu. Tetap pertahankan kedisiplinan di kelas.',
            'izin' => 'Mohon lengkapi keterangan izin agar wali kelas dapat memperbarui rekap absensi.',
            'sakit' => 'Semoga lekas pulih. Materi hari ini bisa dipelajari kembali melalui LMS.',
            'alfa' => 'Mohon segera konfirmasi alasan ketidakhadiran agar dapat ditindaklanjuti wali kelas.',
        ];
        $characterTemplates = [
            ['prestasi', 'Tanggung Jawab', 10, 'Aktif kelas', 'Aktif bertanya dan membantu diskusi kelompok.'],
            ['prestasi', 'Kerjasama', 20, 'Juara lomba', 'Mewakili sekolah dan meraih prestasi dalam lomba.'],
            ['pelanggaran', 'Disiplin', -5, 'Terlambat', 'Datang terlambat saat jam pelajaran pertama.'],
            ['pelanggaran', 'Disiplin', -10, 'Tidak pakai seragam', 'Seragam tidak sesuai ketentuan sekolah.'],
            ['pelanggaran', 'Tanggung Jawab', -20, 'Bolos', 'Tidak hadir pada jam pelajaran tanpa keterangan.'],
            ['prestasi', 'Kejujuran', 15, 'Jujur saat evaluasi', 'Menunjukkan sikap jujur saat kegiatan evaluasi.'],
        ];

        foreach ($students as $index => $item) {
            $status = $statuses[$index % count($statuses)];
            $character = $characterTemplates[$index % count($characterTemplates)];
            [$pointType, $pointCategory, $pointValue, $pointTitle, $pointDescription] = $character;

            foreach ($courses->take(3) as $courseIndex => $course) {
                Grade::create([
                    'student_id' => $item->id,
                    'course_id' => $course->id,
                    'score' => 72 + (($index * 4 + $courseIndex * 7) % 28),
                    'semester' => 'Ganjil',
                ]);
            }

            Attendance::create([
                'student_id' => $item->id,
                'date' => now()->subDays($index % 5),
                'status' => $status,
            ]);

            StudentPoint::firstOrCreate(
                [
                    'student_id' => $item->id,
                    'title' => $pointTitle,
                    'occurred_at' => now()->subDays($index % 7)->toDateString(),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'type' => $pointType,
                    'category' => $pointCategory,
                    'point' => $pointValue,
                    'description' => $pointDescription,
                ]
            );

            Message::create([
                'sender_id' => $teacher->id,
                'receiver_id' => $item->id,
                'body' => 'Halo '.$item->name.', '.$communicationTopics[$status],
                'read_at' => $index % 3 === 0 ? now()->subHours($index + 1) : null,
            ]);

            if ($index % 4 === 0) {
                Message::create([
                    'sender_id' => $item->id,
                    'receiver_id' => $teacher->id,
                    'body' => 'Baik Bu Maya, saya sudah menerima informasi dan akan menindaklanjuti.',
                    'read_at' => now()->subHours($index + 2),
                ]);
            }
        }

        foreach ($cleanStudents as $index => $item) {
            foreach ($courses->take(3) as $courseIndex => $course) {
                Grade::updateOrCreate(
                    [
                        'student_id' => $item->id,
                        'course_id' => $course->id,
                        'semester' => 'Ganjil',
                    ],
                    [
                        'score' => 78 + (($index * 5 + $courseIndex * 4) % 18),
                    ]
                );
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $item->id,
                    'date' => now()->subDays($index % 4)->toDateString(),
                ],
                [
                    'status' => ['hadir', 'hadir', 'hadir', 'izin'][$index % 4],
                ]
            );

            StudentPoint::updateOrCreate(
                [
                    'student_id' => $item->id,
                    'title' => ['Rajin membaca', 'Membantu teman', 'Aktif piket', 'Rapi dan tertib'][$index % 4],
                    'occurred_at' => now()->subDays($index + 1)->toDateString(),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'type' => 'prestasi',
                    'category' => ['Tanggung Jawab', 'Kerjasama', 'Disiplin', 'Kejujuran'][$index % 4],
                    'point' => [10, 15, 10, 15][$index % 4],
                    'description' => [
                        'Siswa konsisten membaca buku pengayaan sebelum pembelajaran dimulai.',
                        'Siswa membantu teman memahami tugas tanpa diminta guru.',
                        'Siswa menjalankan jadwal piket dengan tertib dan menjaga kebersihan kelas.',
                        'Siswa menunjukkan sikap rapi, tertib, dan jujur dalam kegiatan harian.',
                    ][$index % 4],
                ]
            );

            Message::create([
                'sender_id' => $teacher->id,
                'receiver_id' => $item->id,
                'body' => 'Halo '.$item->name.', pertahankan sikap baik dan kebiasaan belajar yang sudah berjalan.',
                'read_at' => $index % 2 === 0 ? now()->subHours($index + 1) : null,
            ]);
        }

        Message::create([
            'sender_id' => $admin->id,
            'receiver_id' => $teacher->id,
            'body' => 'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',
            'read_at' => null,
        ]);

        $sanctionCases = [
            ['student' => $students[0], 'total' => -20, 'type' => 'Peringatan 1', 'category' => 'Disiplin', 'violation' => 'Bolos jam pelajaran', 'point' => -20, 'description' => 'Siswa meninggalkan jam pelajaran tanpa izin guru piket atau wali kelas.', 'task' => 'Membuat surat pernyataan, meminta paraf wali kelas, dan mengikuti pembinaan disiplin sebelum pelajaran dimulai.'],
            ['student' => $students[1], 'total' => -5, 'type' => 'Teguran lisan', 'category' => 'Disiplin', 'violation' => 'Terlambat masuk kelas', 'point' => -5, 'description' => 'Siswa datang setelah bel masuk pelajaran pertama tanpa alasan yang jelas.', 'task' => 'Mencatat alasan keterlambatan di buku piket dan hadir 10 menit lebih awal selama tiga hari.'],
            ['student' => $students[2], 'total' => -10, 'type' => 'Peringatan ringan', 'category' => 'Disiplin', 'violation' => 'Seragam tidak lengkap', 'point' => -10, 'description' => 'Siswa tidak memakai atribut sekolah sesuai ketentuan harian.', 'task' => 'Melengkapi atribut sekolah dan melapor ke wali kelas pada pemeriksaan berikutnya.'],
            ['student' => $students[3], 'total' => -15, 'type' => 'Pembinaan wali kelas', 'category' => 'Tanggung Jawab', 'violation' => 'Tidak mengerjakan tugas', 'point' => -15, 'description' => 'Siswa tidak mengumpulkan tugas mata pelajaran sesuai tenggat waktu.', 'task' => 'Menyelesaikan tugas tertunda dan menulis refleksi tanggung jawab belajar.'],
            ['student' => $students[4], 'total' => -25, 'type' => 'Peringatan 1', 'category' => 'Disiplin', 'violation' => 'Membuat gaduh di kelas', 'point' => -25, 'description' => 'Siswa mengganggu proses pembelajaran sehingga kelas tidak kondusif.', 'task' => 'Mengikuti pembinaan wali kelas dan membantu menyiapkan kelas selama tiga hari.'],
            ['student' => $students[5], 'total' => -10, 'type' => 'Peringatan ringan', 'category' => 'Tanggung Jawab', 'violation' => 'Tidak membawa buku pelajaran', 'point' => -10, 'description' => 'Siswa tidak membawa buku atau perlengkapan utama saat pembelajaran.', 'task' => 'Membuat daftar perlengkapan belajar dan meminta tanda tangan orang tua.'],
            ['student' => $students[6], 'total' => -15, 'type' => 'Pembinaan guru BK', 'category' => 'Kejujuran', 'violation' => 'Menyontek saat kuis', 'point' => -15, 'description' => 'Siswa terlihat menyalin jawaban teman saat evaluasi singkat.', 'task' => 'Mengulang kuis secara mandiri dan mengikuti pembinaan kejujuran bersama guru BK.'],
            ['student' => $students[7], 'total' => -10, 'type' => 'Teguran tertulis', 'category' => 'Disiplin', 'violation' => 'Tidak mengikuti upacara', 'point' => -10, 'description' => 'Siswa tidak mengikuti upacara bendera tanpa keterangan.', 'task' => 'Membuat rangkuman tata tertib upacara dan mengikuti upacara berikutnya dengan pengawasan wali kelas.'],
            ['student' => $students[8], 'total' => -20, 'type' => 'Peringatan 1', 'category' => 'Tanggung Jawab', 'violation' => 'Meninggalkan kelas tanpa izin', 'point' => -20, 'description' => 'Siswa keluar kelas pada jam pelajaran tanpa izin guru.', 'task' => 'Mengisi surat pernyataan dan melapor ke guru piket selama tiga hari.'],
            ['student' => $students[9], 'total' => -5, 'type' => 'Teguran lisan', 'category' => 'Disiplin', 'violation' => 'Tidak menjaga kebersihan kelas', 'point' => -5, 'description' => 'Siswa meninggalkan sampah di area tempat duduk setelah kegiatan belajar.', 'task' => 'Melaksanakan piket tambahan dan mengingatkan kelompoknya menjaga kebersihan kelas.'],
            ['student' => $students[10], 'total' => -30, 'type' => 'Panggilan orang tua', 'category' => 'Kerjasama', 'violation' => 'Berselisih dengan teman', 'point' => -30, 'description' => 'Siswa terlibat perselisihan dengan teman dan membutuhkan mediasi sekolah.', 'task' => 'Mengikuti mediasi dengan wali kelas, meminta maaf secara tertulis, dan membuat komitmen menjaga sikap.'],
            ['student' => $students[11], 'total' => -10, 'type' => 'Peringatan ringan', 'category' => 'Tanggung Jawab', 'violation' => 'Tidak mengumpulkan buku kontrol', 'point' => -10, 'description' => 'Siswa tidak menyerahkan buku kontrol kegiatan sesuai jadwal.', 'task' => 'Melengkapi buku kontrol dan meminta tanda tangan wali kelas serta orang tua.'],
            ['student' => $students[12], 'total' => -15, 'type' => 'Pembinaan wali kelas', 'category' => 'Disiplin', 'violation' => 'Menggunakan HP saat pelajaran', 'point' => -15, 'description' => 'Siswa menggunakan ponsel saat pembelajaran tanpa instruksi guru.', 'task' => 'Menitipkan ponsel ke wali kelas selama jam belajar dan membuat catatan materi yang tertinggal.'],
            ['student' => $students[13], 'total' => -5, 'type' => 'Teguran lisan', 'category' => 'Disiplin', 'violation' => 'Tidak memakai sepatu hitam', 'point' => -5, 'description' => 'Siswa memakai sepatu yang tidak sesuai aturan seragam sekolah.', 'task' => 'Memakai sepatu sesuai aturan pada hari berikutnya dan melapor ke wali kelas.'],
            ['student' => $students[14], 'total' => -20, 'type' => 'Peringatan 1', 'category' => 'Tanggung Jawab', 'violation' => 'Tidak hadir kegiatan literasi', 'point' => -20, 'description' => 'Siswa tidak mengikuti kegiatan literasi pagi tanpa keterangan.', 'task' => 'Membuat resume bacaan dan mengikuti kegiatan literasi pengganti.'],
            ['student' => $students[15], 'total' => -10, 'type' => 'Peringatan ringan', 'category' => 'Kerjasama', 'violation' => 'Tidak ikut kerja kelompok', 'point' => -10, 'description' => 'Siswa tidak berkontribusi dalam tugas kelompok yang sudah dibagi.', 'task' => 'Menyelesaikan bagian tugas yang belum dikerjakan dan mempresentasikan hasilnya.'],
            ['student' => $students[16], 'total' => -35, 'type' => 'Panggilan orang tua', 'category' => 'Disiplin', 'violation' => 'Terlambat berulang', 'point' => -35, 'description' => 'Siswa terlambat beberapa kali dalam satu pekan sehingga perlu pembinaan lanjutan.', 'task' => 'Hadir bersama orang tua untuk konseling dan melapor ke piket pagi selama lima hari.'],
            ['student' => $students[17], 'total' => -15, 'type' => 'Pembinaan guru BK', 'category' => 'Kejujuran', 'violation' => 'Memberi alasan izin tidak sesuai', 'point' => -15, 'description' => 'Siswa memberikan keterangan izin yang tidak sesuai dengan kondisi sebenarnya.', 'task' => 'Mengklarifikasi keterangan kepada wali kelas dan mengikuti pembinaan kejujuran.'],
            ['student' => $students[18], 'total' => -25, 'type' => 'Peringatan 1', 'category' => 'Tanggung Jawab', 'violation' => 'Merusak fasilitas kelas', 'point' => -25, 'description' => 'Siswa merusak fasilitas kelas karena kurang hati-hati saat kegiatan belajar.', 'task' => 'Memperbaiki atau mengganti fasilitas sesuai arahan sarana prasarana dan membuat surat komitmen.'],
            ['student' => $students[19], 'total' => -10, 'type' => 'Peringatan ringan', 'category' => 'Disiplin', 'violation' => 'Tidak membawa kartu pelajar', 'point' => -10, 'description' => 'Siswa tidak membawa kartu pelajar saat pemeriksaan identitas sekolah.', 'task' => 'Membawa kartu pelajar pada pemeriksaan berikutnya dan melapor ke wali kelas.'],
        ];

        foreach ($sanctionCases as $index => $case) {
            StudentPoint::updateOrCreate(
                [
                    'student_id' => $case['student']->id,
                    'title' => $case['violation'],
                    'occurred_at' => now()->subDays($index + 1)->toDateString(),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'type' => 'pelanggaran',
                    'category' => $case['category'],
                    'point' => $case['point'],
                    'description' => $case['description'],
                ]
            );

            Sanction::updateOrCreate(
                [
                    'student_id' => $case['student']->id,
                    'sanction_type' => $case['type'],
                ],
                [
                    'total_points' => $case['total'],
                    'note' => $case['task'],
                ]
            );
        }

        $newsSeeds = [
            [
                'title' => 'Literasi Pagi Mendorong Kebiasaan Membaca Siswa',
                'category' => 'Literasi',
                'cover_color' => 'emerald',
                'image_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Program literasi pagi dilaksanakan untuk membangun kebiasaan membaca, memperkaya kosakata, dan meningkatkan pemahaman siswa sebelum kegiatan belajar dimulai.',
                'content' => "Sekolah melaksanakan program literasi pagi selama lima belas menit sebelum pembelajaran dimulai. Kegiatan ini memberi ruang bagi siswa untuk membaca buku fiksi, nonfiksi, maupun bahan bacaan tematik sesuai minat masing-masing.\n\nGuru kelas mendampingi siswa dengan mencatat perkembangan bacaan dan mengajak beberapa siswa membagikan isi buku secara singkat. Melalui kegiatan sederhana ini, sekolah berharap budaya membaca dapat tumbuh secara konsisten dan berdampak pada kemampuan memahami materi pelajaran.",
            ],
            [
                'title' => 'Pembelajaran Digital Membantu Guru Memantau Perkembangan Kelas',
                'category' => 'Teknologi Pendidikan',
                'cover_color' => 'sky',
                'image_url' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Pemanfaatan platform digital sekolah membantu guru mencatat kehadiran, membagikan materi, dan memantau perkembangan siswa secara lebih teratur.',
                'content' => "Guru mulai memanfaatkan layanan digital sekolah untuk mendukung proses pembelajaran harian. Materi, rekap kehadiran, dan catatan karakter siswa dapat dipantau dalam satu sistem sehingga koordinasi antara guru, siswa, dan orang tua menjadi lebih mudah.\n\nPenggunaan teknologi ini tidak menggantikan peran guru di kelas, tetapi membantu pekerjaan administrasi agar lebih tertata. Data yang tersimpan juga dapat menjadi bahan evaluasi dalam rapat wali kelas dan tindak lanjut pembinaan siswa.",
            ],
            [
                'title' => 'Sekolah Memperkuat Pembelajaran Berbasis 8 Dimensi Profil Lulusan',
                'category' => 'Kurikulum',
                'cover_color' => 'amber',
                'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Sekolah mulai mengarahkan kegiatan belajar pada 8 dimensi profil lulusan agar siswa tumbuh utuh dalam karakter, nalar, kolaborasi, kemandirian, kesehatan, dan komunikasi.',
                'content' => "Sekolah memperkuat arah pembelajaran dengan menekankan 8 dimensi profil lulusan, yaitu keimanan dan ketakwaan, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, dan komunikasi. Pendekatan ini membantu guru merancang kegiatan belajar yang tidak hanya mengejar capaian akademik, tetapi juga membentuk kebiasaan berpikir, bersikap, dan bekerja sama.\n\nDalam pelaksanaannya, guru mengaitkan materi pelajaran dengan tugas yang mendorong siswa berdiskusi, memecahkan masalah, menjaga kesehatan diri, serta menyampaikan gagasan secara jelas. Sekolah berharap penguatan 8 dimensi ini membuat lulusan lebih siap beradaptasi, bertanggung jawab, dan berkontribusi positif di lingkungan masyarakat.",
            ],
            [
                'title' => 'Kolaborasi Orang Tua dan Sekolah Menguatkan Pembinaan Karakter',
                'category' => 'Karakter',
                'cover_color' => 'rose',
                'image_url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Sekolah mengajak orang tua memperkuat pembinaan karakter siswa melalui komunikasi rutin, pemantauan kehadiran, dan tindak lanjut catatan perilaku.',
                'content' => "Pembinaan karakter siswa membutuhkan kerja sama yang dekat antara sekolah dan keluarga. Melalui komunikasi rutin, orang tua dapat mengetahui perkembangan anak, termasuk kehadiran, prestasi, dan catatan pelanggaran yang perlu ditindaklanjuti.\n\nSekolah mendorong setiap wali kelas untuk menyampaikan informasi secara proporsional dan membangun dialog yang mendukung perubahan positif. Dengan keterlibatan orang tua, pembiasaan disiplin dan tanggung jawab dapat berjalan lebih konsisten di rumah maupun di sekolah.",
            ],
        ];

        foreach ($newsSeeds as $index => $item) {
            News::updateOrCreate(
                ['slug' => Str::slug($item['title']).'-'.$index],
                [
                    'author_id' => $admin->id,
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'cover_color' => $item['cover_color'],
                    'image_url' => $item['image_url'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'published_at' => now()->subDays($index),
                ]
            );
        }

        $this->backfillSchoolIds($school->id);
    }

    private function backfillSchoolIds($schoolId)
    {
        DB::table('users')->whereNull('school_id')->update(['school_id' => $schoolId]);

        foreach ([
            ['student_points', 'student_id'],
            ['sanctions', 'student_id'],
            ['school_notifications', 'user_id'],
            ['news', 'author_id'],
            ['attendances', 'student_id'],
            ['courses', 'teacher_id'],
            ['grades', 'student_id'],
            ['messages', 'sender_id'],
            ['lms_assignments', 'teacher_id'],
        ] as [$table, $userColumn]) {
            if (! DB::getSchemaBuilder()->hasTable($table) || ! DB::getSchemaBuilder()->hasColumn($table, 'school_id')) {
                continue;
            }

            DB::statement("UPDATE {$table} target JOIN users u ON target.{$userColumn} = u.id SET target.school_id = u.school_id WHERE target.school_id IS NULL");
            DB::table($table)->whereNull('school_id')->update(['school_id' => $schoolId]);
        }

        if (DB::getSchemaBuilder()->hasTable('lms_submissions') && DB::getSchemaBuilder()->hasColumn('lms_submissions', 'school_id')) {
            DB::statement('UPDATE lms_submissions target JOIN lms_assignments a ON target.lms_assignment_id = a.id SET target.school_id = a.school_id WHERE target.school_id IS NULL');
            DB::table('lms_submissions')->whereNull('school_id')->update(['school_id' => $schoolId]);
        }
    }
}
