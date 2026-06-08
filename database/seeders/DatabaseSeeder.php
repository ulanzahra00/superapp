<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Message;
use App\Models\News;
use App\Models\Sanction;
use App\Models\SchoolNotification;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Database\Seeder;
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
        $admin = User::updateOrCreate(
            ['email' => 'admin@sekolah.test'],
            [
                'name' => 'Kepala Sekolah',
                'role' => 'admin',
                'phone' => '0811000001',
                'password' => Hash::make('password'),
            ]
        );

        $teacher = User::updateOrCreate(
            ['email' => 'guru@sekolah.test'],
            [
                'name' => 'Ibu Guru Maya',
                'role' => 'guru',
                'phone' => '0811000002',
                'password' => Hash::make('password'),
            ]
        );

        $parent = User::updateOrCreate(
            ['email' => 'ortu@sekolah.test'],
            [
                'name' => 'Bapak Andi',
                'role' => 'orang_tua',
                'phone' => '0811000003',
                'password' => Hash::make('password'),
            ]
        );

        $studentSeeds = [
            ['Andi Pratama', 'siswa@sekolah.test', 'SIS001', 'XI IPA 1', $parent->id],
            ['Siti Rahma', 'siti@sekolah.test', 'SIS002', 'XI IPA 1', null],
            ['Budi Santoso', 'budi@sekolah.test', 'SIS003', 'XI IPA 1', null],
            ['Dewi Lestari', 'dewi@sekolah.test', 'SIS004', 'XI IPA 1', null],
            ['Rizky Maulana', 'rizky@sekolah.test', 'SIS005', 'XI IPA 2', null],
            ['Nadia Putri', 'nadia@sekolah.test', 'SIS006', 'XI IPA 2', null],
            ['Fajar Nugroho', 'fajar@sekolah.test', 'SIS007', 'XI IPA 2', null],
            ['Ayu Wulandari', 'ayu@sekolah.test', 'SIS008', 'XI IPA 2', null],
            ['Raka Saputra', 'raka@sekolah.test', 'SIS009', 'XI IPS 1', null],
            ['Maya Permata', 'maya@sekolah.test', 'SIS010', 'XI IPS 1', null],
            ['Dimas Firmansyah', 'dimas@sekolah.test', 'SIS011', 'XI IPS 1', null],
            ['Intan Maharani', 'intan@sekolah.test', 'SIS012', 'XI IPS 1', null],
            ['Bagas Aditya', 'bagas@sekolah.test', 'SIS013', 'X IPA 1', null],
            ['Citra Anggraini', 'citra@sekolah.test', 'SIS014', 'X IPA 1', null],
            ['Galih Prakoso', 'galih@sekolah.test', 'SIS015', 'X IPA 1', null],
            ['Laras Puspita', 'laras@sekolah.test', 'SIS016', 'X IPA 1', null],
            ['Yoga Ramadhan', 'yoga@sekolah.test', 'SIS017', 'X IPS 1', null],
            ['Putri Amelia', 'putri@sekolah.test', 'SIS018', 'X IPS 1', null],
            ['Arif Hidayat', 'arif@sekolah.test', 'SIS019', 'X IPS 1', null],
            ['Sekar Kinanti', 'sekar@sekolah.test', 'SIS020', 'X IPS 1', null],
        ];

        $students = collect($studentSeeds)->map(function ($item) {
            [$name, $email, $nis, $className, $parentId] = $item;

            return User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => 'siswa',
                    'parent_id' => $parentId,
                    'nis' => $nis,
                    'class_name' => $className,
                    'password' => Hash::make('password'),
                ]
            );
        });

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

        Message::create([
            'sender_id' => $admin->id,
            'receiver_id' => $teacher->id,
            'body' => 'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',
            'read_at' => null,
        ]);

        $sanctionCases = [
            [
                'student' => $students[0],
                'total' => -20,
                'type' => 'Peringatan 1',
                'violation' => 'Bolos',
                'point' => -20,
                'description' => 'Siswa tidak hadir pada jam pelajaran tanpa keterangan dari orang tua atau wali kelas.',
                'task' => 'Yang harus dilakukan: membuat surat pernyataan, meminta paraf wali kelas, dan mengikuti pembinaan disiplin 15 menit sebelum pelajaran.',
            ],
            [
                'student' => $students[4],
                'total' => -50,
                'type' => 'Panggilan orang tua',
                'violation' => 'Terlambat berulang dan tidak lengkap atribut',
                'point' => -50,
                'description' => 'Siswa beberapa kali datang terlambat dan tidak memakai atribut sekolah secara lengkap.',
                'task' => 'Yang harus dilakukan: hadir bersama orang tua/wali untuk konseling, menyusun jadwal belajar, dan melapor ke wali kelas selama 5 hari.',
            ],
            [
                'student' => $students[10],
                'total' => -20,
                'type' => 'Peringatan 1',
                'violation' => 'Tidak mengerjakan tugas kelas',
                'point' => -20,
                'description' => 'Siswa tidak menyelesaikan tugas kelas sesuai batas waktu yang sudah diberikan guru.',
                'task' => 'Yang harus dilakukan: menyelesaikan tugas tertunda, menulis refleksi tanggung jawab, dan menyerahkan laporan ke guru mapel.',
            ],
            [
                'student' => $students[16],
                'total' => -100,
                'type' => 'Skorsing',
                'violation' => 'Pelanggaran disiplin berat',
                'point' => -100,
                'description' => 'Siswa melakukan pelanggaran disiplin berat yang membutuhkan pembinaan khusus dari sekolah.',
                'task' => 'Yang harus dilakukan: skorsing pembinaan 2 hari, tugas belajar mandiri terpantau, dan sesi konseling sebelum kembali ke kelas.',
            ],
            [
                'student' => $students[18],
                'total' => -20,
                'type' => 'Peringatan 1',
                'violation' => 'Tidak mengikuti upacara',
                'point' => -20,
                'description' => 'Siswa tidak mengikuti kegiatan upacara sekolah tanpa alasan yang dapat dipertanggungjawabkan.',
                'task' => 'Yang harus dilakukan: mengikuti pembinaan kedisiplinan, membantu piket kelas, dan membuat rangkuman tata tertib sekolah.',
            ],
        ];

        foreach ($sanctionCases as $case) {
            StudentPoint::updateOrCreate(
                [
                    'student_id' => $case['student']->id,
                    'title' => $case['violation'],
                    'occurred_at' => now()->subDays(1)->toDateString(),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'type' => 'pelanggaran',
                    'category' => 'Disiplin',
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

        foreach (['Transformasi Digital Sekolah', 'Agenda Projek P5', 'Prestasi Siswa Pekan Ini'] as $index => $title) {
            News::updateOrCreate(
                ['slug' => Str::slug($title).'-'.$index],
                [
                    'author_id' => $admin->id,
                    'title' => $title,
                    'category' => $index === 0 ? 'Pengumuman' : 'Kegiatan',
                    'cover_color' => ['emerald', 'amber', 'rose'][$index],
                    'excerpt' => 'Informasi terbaru untuk warga sekolah dan orang tua.',
                    'content' => 'Sekolah menghadirkan layanan digital terpadu untuk absensi, LMS, nilai, komunikasi, berita, serta monitoring karakter siswa.',
                    'published_at' => now()->subDays($index),
                ]
            );
        }
    }
}
