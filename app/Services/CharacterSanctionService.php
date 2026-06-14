<?php

namespace App\Services;

use App\Models\Sanction;
use App\Models\SchoolNotification;
use App\Models\StudentPoint;
use App\Models\User;

class CharacterSanctionService
{
    public function __construct(
        private FonnteService $fonnte
    ) {
    }

    public const RULES = [
        -150 => 'Rekomendasi tindakan berat',
        -100 => 'Skorsing',
        -30 => 'Panggilan orang tua',
        -20 => 'Peringatan 1',
    ];

    public function recordPoint(array $data): StudentPoint
    {
        $point = StudentPoint::create($data);
        $student = User::with('parent')->findOrFail($data['student_id']);

        if ($point->type === 'pelanggaran') {
            $this->notifyParent(
                $student,
                'Pelanggaran siswa',
                $student->name.' mendapat '.$point->point.' poin: '.$point->title,
                'warning'
            );
        }

        $this->syncSanction($student);

        return $point;
    }

    public function syncSanction(User $student): ?Sanction
    {
        $total = $student->studentPoints()->sum('point');
        $type = $this->sanctionFor($total);

        if (! $type) {
            return null;
        }

        $latest = $student->sanctions()->latest()->first();
        if ($latest && $latest->sanction_type === $type) {
            return $latest;
        }

        $sanction = Sanction::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'total_points' => $total,
            'sanction_type' => $type,
            'note' => 'Sanksi otomatis berdasarkan total poin karakter.',
        ]);

        $this->notifyParent(
            $student,
            'Sanksi otomatis: '.$type,
            $student->name.' mencapai total poin '.$total.'. Tindak lanjut: '.$type.'.',
            'danger'
        );

        // Kirim notifikasi WhatsApp ke grup sekolah jika sanksi "Panggilan orang tua" (<= -30 poin)
        if ($type === 'Panggilan orang tua') {
            $message = "⚠️ INFO PELANGGARAN SISWA ⚠️\n\n"
                . "Nama Siswa: {$student->name}\n"
                . "Total Poin: {$total}\n\n"
                . "Siswa yang bersangkutan telah mencapai batas poin pelanggaran (-30). "
                . "Sistem telah menerbitkan surat panggilan orang tua. "
                . "Mohon Bapak/Ibu Wali Kelas untuk menindaklanjutinya.";

            $this->fonnte->sendToGroup($message);
        }

        return $sanction;
    }

    public function sanctionFor(int $total): ?string
    {
        foreach (self::RULES as $threshold => $type) {
            if ($total <= $threshold) {
                return $type;
            }
        }

        return null;
    }

    private function notifyParent(User $student, string $title, string $message, string $level): void
    {
        if (! $student->parent_id) {
            return;
        }

        SchoolNotification::create([
            'school_id' => $student->school_id,
            'user_id' => $student->parent_id,
            'title' => $title,
            'message' => $message,
            'level' => $level,
        ]);
    }
}