<?php

namespace App\Services;

use App\Models\Sanction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfSuratPanggilanService
{
    /**
     * Generate PDF Surat Panggilan Orang Tua
     */
    public function generate(Sanction $sanction): string
    {
        $student = $sanction->student;
        $school = $student->school;

        // Ambil nama kota dari alamat sekolah (ambil kata pertama sebelum koma)
        $kota = $school->city ?? '';
        if (empty($kota) && !empty($school->address)) {
            $parts = explode(',', $school->address);
            $kota = trim(end($parts)) ?: 'Kota';
        }
        if (empty($kota)) {
            $kota = 'Kota';
        }

        $data = [
            'school'        => $school,
            'student'       => $student,
            'kota'          => $kota,
            'nomorSurat'    => 'SP/' . str_pad($sanction->id, 4, '0', STR_PAD_LEFT) . '/' . date('Y'),
            'totalPoints'   => $sanction->total_points,
            'sanctionType'  => $sanction->sanction_type,
            'hariTanggal'   => now()->locale('id')->isoFormat('dddd, D MMMM Y'),
            'waktu'         => now()->format('H:i') . ' WIB',
            'tanggalSurat'  => now()->locale('id')->isoFormat('D MMMM Y'),
            'teacherName'   => 'Kepala Sekolah',
            'teacherNip'    => '-',
        ];

        $pdf = Pdf::loadView('pdf.surat-panggilan-orangtua', $data);
        $pdf->setPaper('A4', 'portrait');

        // Simpan ke storage
        $filename = 'surat-panggilan-' . $sanction->id . '-' . $student->nis . '-' . date('Ymd') . '.pdf';
        $path = 'surat-panggilan/' . $filename;

        // Pastikan direktori ada
        $storagePath = storage_path('app/public/surat-panggilan');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        // Simpan PDF
        $pdf->save(storage_path('app/public/' . $path));

        return $path;
    }

    /**
     * Dapatkan URL publik untuk PDF
     */
    public function getPublicUrl(string $path): string
    {
        return url('storage/' . $path);
    }

    /**
     * Hapus file PDF
     */
    public function delete(string $path): bool
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}