<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Panggilan Orang Tua</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .kop-surat h1 {
            font-size: 16pt;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat h2 {
            font-size: 14pt;
            margin: 5px 0;
            font-weight: bold;
        }
        .kop-surat p {
            margin: 2px 0;
            font-size: 11pt;
        }
        .nomor-surat {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            text-decoration: underline;
        }
        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
        }
        .isi-surat p {
            margin: 10px 0;
            text-indent: 1.5cm;
        }
        .data-siswa {
            margin: 15px 0;
            padding-left: 1.5cm;
        }
        .data-siswa td {
            padding: 3px 10px;
            vertical-align: top;
        }
        .data-siswa .label {
            width: 120px;
        }
        .tabel-poin {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .tabel-poin th, .tabel-poin td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .tabel-poin th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .ttd {
            margin-top: 50px;
            text-align: right;
        }
        .ttd p {
            margin: 5px 0;
        }
        .ttd .nama {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 10pt;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h1>PEMERINTAH KABUPATEN/KOTA</h1>
        <h2>{{ strtoupper($school->name ?? 'SEKOLAH') }}</h2>
        <p>{{ $school->address ?? 'Alamat Sekolah' }}</p>
        <p>Email: {{ $school->email ?? '-' }} | Telp: {{ $school->phone ?? '-' }}</p>
    </div>

    <div class="nomor-surat">
        Nomor: {{ $nomorSurat }}
    </div>

    <div class="judul-surat">
        SURAT PANGGILAN ORANG TUA/WALI SISWA
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini, Wali Kelas dari:</p>

        <table class="data-siswa">
            <tr>
                <td class="label">Nama Siswa</td>
                <td>: {{ $student->name }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td>: {{ $student->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td>: {{ $student->class_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Total Poin</td>
                <td>: {{ $totalPoints }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Sanksi</td>
                <td>: {{ $sanctionType }}</td>
            </tr>
        </table>

        <p>Dengan ini mengharapkan kehadiran Bapak/Ibu Orang Tua/Wali dari siswa tersebut pada:</p>

        <table style="margin-left: 1.5cm; margin-top: 15px;">
            <tr>
                <td style="width: 120px; vertical-align: top;">Hari/Tanggal</td>
                <td>: {{ $hariTanggal }}</td>
            </tr>
            <tr>
                <td style="width: 120px; vertical-align: top;">Waktu</td>
                <td>: {{ $waktu }}</td>
            </tr>
            <tr>
                <td style="width: 120px; vertical-align: top;">Tempat</td>
                <td>: {{ $school->name ?? 'Sekolah' }}</td>
            </tr>
            <tr>
                <td style="width: 120px; vertical-align: top;">Keperluan</td>
                <td>: Membahas perkembangan dan permasalahan putra/putri Bapak/Ibu terkait pelanggaran yang telah dilakukan.</td>
            </tr>
        </table>

        <p>Demikian surat panggilan ini kami sampaikan. Atas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>

    <div class="ttd">
        <p>{{ $kota }}, {{ $tanggalSurat }}</p>
        <p>Kepala Sekolah,</p>
        <br><br>
        <p class="nama">( {{ $teacherName ?? '________________' }} )</p>
        <p>NIP. {{ $teacherNip ?? '-' }}</p>
    </div>

    <div class="footer-note">
        <p>Surat ini dibuat secara otomatis oleh sistem.</p>
    </div>
</body>
</html>