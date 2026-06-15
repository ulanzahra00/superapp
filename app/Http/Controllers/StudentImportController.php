<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentImportController extends Controller
{
    public function template()
    {
        $headers = [
            'nama_siswa',
            'email_siswa',
            'nis',
            'kelas',
            'nama_orang_tua',
            'email_orang_tua',
            'telepon',
            'password',
        ];

        $rows = [
            ['Nama Siswa Contoh', 'siswa.contoh@sekolah.test', 'SIS021', 'X IPA 1', 'Nama Orang Tua', 'ortu.contoh@sekolah.test', '081234567890', 'password'],
            ['Nama Siswa Kedua', 'siswa.kedua@sekolah.test', 'SIS022', 'X IPA 2', '', '', '081234567891', 'password'],
        ];

        return response()->streamDownload(function () use ($headers, $rows) {
            $path = $this->buildXlsxTemplate($headers, $rows);
            readfile($path);
            @unlink($path);
        }, 'template-import-siswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'student_file' => ['required', 'file', 'mimes:xls,xlsx,csv,txt,html', 'max:2048'],
        ]);

        $rows = $this->readUploadedRows($request->file('student_file')->getRealPath(), $request->file('student_file')->getClientOriginalExtension());
        $rawHeader = array_shift($rows);

        if (! $rawHeader) {
            return back()->withErrors(['student_file' => 'Template kosong atau tidak bisa dibaca.']);
        }

        $header = $rawHeader;
        $header = array_map(function ($value) {
            return $this->normalizeHeader($value);
        }, $header);

        $requiredHeaders = ['name', 'nis', 'class_name'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if ($missingHeaders) {
            return back()->withErrors([
                'student_file' => 'Template tidak sesuai. Kolom wajib: nama_siswa, nis, kelas.',
            ]);
        }

        $imported = 0;
        $skipped = [];
        $seenEmails = [];
        $schoolId = $request->user()->school_id;
        $duplicateEmails = $this->duplicateEmails($rows, $header);
        $passwordHashes = [];
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = array_pad($row, count($header), null);
            $data = array_combine($header, array_slice($row, 0, count($header)));
            $data = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $data);

            if (! array_filter($data)) {
                continue;
            }

            if (! empty($data['password']) && strlen($data['password']) < 6) {
                $data['password'] = 'password';
            }

            $data['class_name'] = $this->normalizeClassName($data['class_name'] ?? '');

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'nis' => ['required', 'string', 'max:255'],
                'class_name' => ['required', 'string', 'max:255'],
                'parent_name' => ['nullable', 'string', 'max:255'],
                'parent_email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'password' => ['nullable'],
            ]);

            if ($validator->fails()) {
                $skipped[] = 'Baris '.$rowNumber.': '.$validator->errors()->first();
                continue;
            }

            $student = User::where('school_id', $schoolId)->where('nis', $data['nis'])->first();
            $email = $this->resolveStudentEmail($data, $student, $seenEmails, $duplicateEmails, $schoolId);
            $seenEmails[] = Str::lower($email);

            $parentId = null;
            if (! empty($data['parent_email'])) {
                $parent = User::updateOrCreate(
                    ['email' => $data['parent_email']],
                    [
                        'school_id' => $schoolId,
                        'name' => $data['parent_name'] ?: 'Orang Tua '.$data['name'],
                        'role' => 'orang_tua',
                        'password' => $this->passwordHash($data['password'] ?: 'password', $passwordHashes),
                    ]
                );
                $parentId = $parent->id;
            }

            User::updateOrCreate(
                ['school_id' => $schoolId, 'nis' => $data['nis']],
                [
                    'school_id' => $schoolId,
                    'name' => $data['name'],
                    'email' => $email,
                    'role' => 'siswa',
                    'parent_id' => $parentId,
                    'class_name' => $data['class_name'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $this->passwordHash($data['password'] ?: 'password', $passwordHashes),
                ]
            );

            $imported++;
        }

        $message = $imported.' data siswa berhasil diimpor atau diperbarui.';
        if ($skipped) {
            $message .= ' '.count($skipped).' baris dilewati: '.implode(' ', array_slice($skipped, 0, 3));
        }

        return redirect()->route('dashboard')->with('status', $message);
    }

    public function destroySelected(Request $request)
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa yang akan dihapus.',
        ]);

        $deleted = User::where('role', 'siswa')
            ->where('school_id', $request->user()->school_id)
            ->whereIn('id', $data['student_ids'])
            ->delete();

        return redirect()->route('dashboard')->with('status', $deleted.' data siswa berhasil dihapus.');
    }

    private function readUploadedRows($path, $extension)
    {
        if (Str::lower($extension) === 'xls') {
            return $this->readExcelTableRows($path);
        }

        if (Str::lower($extension) === 'xlsx') {
            return $this->readXlsxRows($path);
        }

        return $this->readCsvRows($path);
    }

    private function normalizeClassName($className)
    {
        $className = trim((string) $className);
        $className = preg_replace('/\s+/', ' ', $className);

        return preg_replace('/^Kelas\s+(\d+)\s*([A-Za-z])$/i', 'Kelas $1 $2', $className);
    }

    private function duplicateEmails(array $rows, array $header)
    {
        $emails = [];

        foreach ($rows as $row) {
            $row = array_pad($row, count($header), null);
            $data = array_combine($header, array_slice($row, 0, count($header)));
            $email = Str::lower(trim((string) ($data['email'] ?? '')));

            if ($email !== '') {
                $emails[$email] = ($emails[$email] ?? 0) + 1;
            }
        }

        return array_keys(array_filter($emails, function ($count) {
            return $count > 1;
        }));
    }

    private function passwordHash($password, array &$passwordHashes)
    {
        $password = (string) $password;

        if (! isset($passwordHashes[$password])) {
            $passwordHashes[$password] = Hash::make($password);
        }

        return $passwordHashes[$password];
    }

    private function resolveStudentEmail(array $data, ?User $student, array $seenEmails, array $duplicateEmails, $schoolId)
    {
        $email = Str::lower((string) ($data['email'] ?? ''));

        if ($email !== '' && ! in_array($email, $seenEmails, true) && ! in_array($email, $duplicateEmails, true)) {
            $owner = User::where('email', $email)->first();

            if (! $owner || ($student && $owner->id === $student->id)) {
                return $email;
            }
        }

        return $this->generateStudentEmail($data['nis'], $student ? $student->id : null, $schoolId);
    }

    private function generateStudentEmail($nis, $currentUserId = null, $schoolId = null)
    {
        $base = Str::slug((string) $nis) ?: 'siswa';
        $schoolSuffix = $schoolId ? '.s'.$schoolId : '';
        $email = 'siswa-'.$base.$schoolSuffix.'@sekolah.test';
        $suffix = 2;

        while (User::where('email', $email)
            ->when($currentUserId, function ($query) use ($currentUserId) {
                $query->where('id', '!=', $currentUserId);
            })
            ->exists()) {
            $email = 'siswa-'.$base.'-'.$suffix.'@sekolah.test';
            $suffix++;
        }

        return $email;
    }

    private function readCsvRows($path)
    {
        $file = fopen($path, 'r');
        $firstLine = $this->readNextCsvLine($file);

        if (! $firstLine) {
            fclose($file);

            return [];
        }

        $delimiter = $this->detectDelimiter($firstLine);
        if (Str::startsWith(Str::lower(trim($firstLine)), 'sep=')) {
            $delimiter = substr(trim($firstLine), 4, 1) ?: ';';
            $firstLine = $this->readNextCsvLine($file);
        }

        if (! $firstLine) {
            fclose($file);

            return [];
        }

        $rows = [str_getcsv($firstLine, $delimiter)];

        while (($line = fgets($file)) !== false) {
            $rows[] = str_getcsv($line, $delimiter);
        }

        fclose($file);

        return $rows;
    }

    private function normalizeHeader($value)
    {
        $key = Str::snake(trim($value));
        $aliases = [
            'nama' => 'name',
            'nama_siswa' => 'name',
            'email_siswa' => 'email',
            'kelas' => 'class_name',
            'nama_orang_tua' => 'parent_name',
            'email_orang_tua' => 'parent_email',
            'telepon' => 'phone',
            'no_hp' => 'phone',
            'nomor_hp' => 'phone',
        ];

        return $aliases[$key] ?? $key;
    }

    private function readNextCsvLine($file)
    {
        while (($line = fgets($file)) !== false) {
            $line = preg_replace('/^\xEF\xBB\xBF/', '', $line);

            if (trim($line) !== '') {
                return $line;
            }
        }

        return null;
    }

    private function detectDelimiter($line)
    {
        $delimiters = [';', ',', "\t"];
        $bestDelimiter = ';';
        $highestCount = 0;

        foreach ($delimiters as $delimiter) {
            $count = substr_count($line, $delimiter);

            if ($count > $highestCount) {
                $highestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    private function buildExcelTableTemplate(array $headers, array $rows)
    {
        $html = "\xEF\xBB\xBF".'<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        th { background: #d9ead3; border: 1px solid #6aa84f; font-weight: bold; padding: 8px; text-align: left; }
        td { border: 1px solid #b7b7b7; padding: 8px; mso-number-format:"\@"; }
        .note { color: #666666; font-size: 10pt; }
    </style>
</head>
<body>
    <table>
        <thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th>'.$this->escapeXml($header).'</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';

            foreach ($row as $value) {
                $html .= '<td>'.$this->escapeXml($value).'</td>';
            }

            $html .= '</tr>';
        }

        for ($index = 0; $index < 20; $index++) {
            $html .= '<tr>';

            foreach ($headers as $header) {
                $html .= '<td></td>';
            }

            $html .= '</tr>';
        }

        return $html.'</tbody></table>
    <p class="note">Isi data siswa mulai dari baris kosong di bawah contoh. Kolom nama_siswa, nis, dan kelas wajib diisi. Email siswa boleh dikosongkan, sistem akan membuat email otomatis dari NIS.</p>
</body>
</html>';
    }

    private function buildXlsxTemplate(array $headers, array $rows)
    {
        $blankRows = array_fill(0, 20, array_fill(0, count($headers), ''));
        $allRows = array_merge([$headers], $rows, $blankRows);
        $lastColumn = $this->columnName(count($headers));
        $lastRow = count($allRows);
        $range = 'A1:'.$lastColumn.$lastRow;

        $strings = [];
        $stringIndexes = [];
        foreach ($allRows as $row) {
            foreach ($row as $value) {
                $value = (string) $value;

                if (! array_key_exists($value, $stringIndexes)) {
                    $stringIndexes[$value] = count($strings);
                    $strings[] = $value;
                }
            }
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'student-template-');
        $path = $temporaryPath.'.xlsx';
        @unlink($temporaryPath);

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
<Override PartName="/xl/tables/table1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.table+xml"/>
</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<bookViews><workbookView/></bookViews>
<sheets><sheet name="Data Siswa" sheetId="1" r:id="rId1"/></sheets>
</workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');
        $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/table" Target="../tables/table1.xml"/>
</Relationships>');
        $zip->addFromString('xl/styles.xml', $this->buildXlsxStylesXml());
        $zip->addFromString('xl/sharedStrings.xml', $this->buildSharedStringsXml($strings));
        $zip->addFromString('xl/tables/table1.xml', $this->buildTableXml($headers, $range));
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildWorksheetXml($allRows, $stringIndexes, $range));
        $zip->close();

        return $path;
    }

    private function buildWorksheetXml(array $rows, array $stringIndexes, $range)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<dimension ref="'.$range.'"/>
<sheetViews><sheetView workbookViewId="0" showGridLines="1"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>
<sheetFormatPr defaultRowHeight="18"/>
<cols>
<col min="1" max="1" width="24" customWidth="1"/>
<col min="2" max="2" width="30" customWidth="1"/>
<col min="3" max="3" width="14" customWidth="1"/>
<col min="4" max="4" width="14" customWidth="1"/>
<col min="5" max="5" width="24" customWidth="1"/>
<col min="6" max="6" width="30" customWidth="1"/>
<col min="7" max="7" width="18" customWidth="1"/>
<col min="8" max="8" width="16" customWidth="1"/>
</cols>
<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $height = $rowIndex === 0 ? 24 : 22;
            $xml .= '<row r="'.$excelRow.'" ht="'.$height.'" customHeight="1">';

            foreach ($row as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1).$excelRow;
                $style = $rowIndex === 0 ? 1 : 2;
                $xml .= '<c r="'.$cell.'" s="'.$style.'" t="s"><v>'.$stringIndexes[(string) $value].'</v></c>';
            }

            $xml .= '</row>';
        }

        return $xml.'</sheetData><autoFilter ref="'.$range.'"/><tableParts count="1"><tablePart r:id="rId1"/></tableParts></worksheet>';
    }

    private function buildXlsxStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="2">
<font><sz val="11"/><name val="Calibri"/></font>
<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
</fonts>
<fills count="3">
<fill><patternFill patternType="none"/></fill>
<fill><patternFill patternType="gray125"/></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FF0F766E"/><bgColor indexed="64"/></patternFill></fill>
</fills>
<borders count="2">
<border><left/><right/><top/><bottom/><diagonal/></border>
<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border>
</borders>
<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
<cellXfs count="3">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
<xf numFmtId="49" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>
</cellXfs>
<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>';
    }

    private function buildSharedStringsXml(array $strings)
    {
        $items = '';

        foreach ($strings as $string) {
            $items .= '<si><t>'.$this->escapeXml($string).'</t></si>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">'.$items.'</sst>';
    }

    private function buildTableXml(array $headers, $range)
    {
        $columns = '';

        foreach ($headers as $index => $header) {
            $columns .= '<tableColumn id="'.($index + 1).'" name="'.$this->escapeXml($header).'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<table xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" id="1" name="DataSiswa" displayName="DataSiswa" ref="'.$range.'" totalsRowShown="0">
<autoFilter ref="'.$range.'"/>
<tableColumns count="'.count($headers).'">'.$columns.'</tableColumns>
<tableStyleInfo name="TableStyleMedium2" showFirstColumn="0" showLastColumn="0" showRowStripes="1" showColumnStripes="0"/>
</table>';
    }

    private function columnName($index)
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function readXlsxRows($path)
    {
        if (! class_exists('ZipArchive')) {
            return [];
        }

        $zipClass = 'ZipArchive';
        $zip = new $zipClass();

        if ($zip->open($path) !== true) {
            return [];
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        $zip->close();

        if (! $sheetXml) {
            return [];
        }

        $sharedStrings = $this->readSharedStrings($sharedStringsXml);
        $sheet = simplexml_load_string($sheetXml);
        $namespace = $sheet->getNamespaces(true)[''] ?? null;
        $sheetData = $namespace ? $sheet->children($namespace)->sheetData : $sheet->sheetData;
        $rows = [];

        foreach ($sheetData->children($namespace)->row as $row) {
            $values = [];

            foreach ($row->children($namespace)->c as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) ($attributes['r'] ?? '');
                $columnIndex = $this->columnIndex($reference);
                $values[$columnIndex] = $this->readXlsxCellValue($cell, $namespace, $sharedStrings);
            }

            if ($values) {
                ksort($values);
                $lastColumn = max(array_keys($values));
                $rowValues = [];

                for ($index = 0; $index <= $lastColumn; $index++) {
                    $rowValues[] = $values[$index] ?? null;
                }

                $rows[] = $rowValues;
            }
        }

        return $rows;
    }

    private function readExcelTableRows($path)
    {
        $html = file_get_contents($path);

        if (! $html) {
            return [];
        }

        $internalErrors = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $rows = [];
        foreach ($dom->getElementsByTagName('tr') as $tableRow) {
            $values = [];

            foreach ($tableRow->childNodes as $cell) {
                if (in_array($cell->nodeName, ['th', 'td'], true)) {
                    $values[] = trim($cell->textContent);
                }
            }

            if ($values && array_filter($values)) {
                $rows[] = $values;
            }
        }

        return $rows;
    }

    private function readSharedStrings($xml)
    {
        if (! $xml) {
            return [];
        }

        $shared = simplexml_load_string($xml);
        $namespace = $shared->getNamespaces(true)[''] ?? null;
        $strings = [];

        foreach ($shared->children($namespace)->si as $item) {
            $strings[] = (string) ($item->children($namespace)->t ?? '');
        }

        return $strings;
    }

    private function readXlsxCellValue($cell, $namespace, array $sharedStrings)
    {
        $attributes = $cell->attributes();
        $type = (string) ($attributes['t'] ?? '');
        $children = $cell->children($namespace);

        if ($type === 'inlineStr') {
            return (string) ($children->is->children($namespace)->t ?? '');
        }

        $value = (string) ($children->v ?? '');

        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? '';
        }

        return $value;
    }

    private function columnIndex($reference)
    {
        preg_match('/^([A-Z]+)/', $reference, $matches);
        $letters = $matches[1] ?? 'A';
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function escapeXml($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
