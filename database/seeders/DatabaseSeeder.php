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
            ['Andi Pratama', 'siswa@sekolah.test', 'SIS001', 'Kelas 1A', $parent->id],
            ['Siti Rahma', 'siti@sekolah.test', 'SIS002', 'Kelas 1B', null],
            ['Budi Santoso', 'budi@sekolah.test', 'SIS003', 'Kelas 2A', null],
            ['Dewi Lestari', 'dewi@sekolah.test', 'SIS004', 'Kelas 2B', null],
            ['Rizky Maulana', 'rizky@sekolah.test', 'SIS005', 'Kelas 3A', null],
            ['Nadia Putri', 'nadia@sekolah.test', 'SIS006', 'Kelas 3B', null],
            ['Fajar Nugroho', 'fajar@sekolah.test', 'SIS007', 'Kelas 4A', null],
            ['Ayu Wulandari', 'ayu@sekolah.test', 'SIS008', 'Kelas 4B', null],
            ['Raka Saputra', 'raka@sekolah.test', 'SIS009', 'Kelas 5A', null],
            ['Maya Permata', 'maya@sekolah.test', 'SIS010', 'Kelas 5B', null],
            ['Dimas Firmansyah', 'dimas@sekolah.test', 'SIS011', 'Kelas 6A', null],
            ['Intan Maharani', 'intan@sekolah.test', 'SIS012', 'Kelas 6B', null],
            ['Bagas Aditya', 'bagas@sekolah.test', 'SIS013', 'Kelas 1A', null],
            ['Citra Anggraini', 'citra@sekolah.test', 'SIS014', 'Kelas 2A', null],
            ['Galih Prakoso', 'galih@sekolah.test', 'SIS015', 'Kelas 3A', null],
            ['Laras Puspita', 'laras@sekolah.test', 'SIS016', 'Kelas 4A', null],
            ['Yoga Ramadhan', 'yoga@sekolah.test', 'SIS017', 'Kelas 5A', null],
            ['Putri Amelia', 'putri@sekolah.test', 'SIS018', 'Kelas 6A', null],
            ['Arif Hidayat', 'arif@sekolah.test', 'SIS019', 'Kelas 5B', null],
            ['Sekar Kinanti', 'sekar@sekolah.test', 'SIS020', 'Kelas 6B', null],
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
