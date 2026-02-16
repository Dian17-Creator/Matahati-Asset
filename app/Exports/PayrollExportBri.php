<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PayrollExportBri implements
    FromCollection,
    WithMapping,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting
{
    protected $data;
    private $fileName;
    public function __construct($data, $fileName = null)
    {
        $this->data     = $data;
        $this->fileName = $fileName ?? 'payroll_bri.xlsx';
    }
    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'REKENING',
            'NOMINAL',
            'EMAIL',
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // REKENING
            'B' => NumberFormat::FORMAT_TEXT, // NOMINAL
            'C' => NumberFormat::FORMAT_TEXT, // EMAIL
        ];
    }

    public function map($row): array
    {
        $rekening = data_get($row, 'user.caccnumber')
            ?? data_get($row, 'caccnumber')
            ?? data_get($row, 'nomor_rekening')
            ?? data_get($row, 'rekening')
            ?? '';

        $email = data_get($row, 'user.cmailaddress')
            ?? data_get($row, 'user.email')
            ?? data_get($row, 'cmailaddress')
            ?? data_get($row, 'email')
            ?? '';
        $nominalRaw = data_get($row, 'total_gaji')
            ?? data_get($row, 'gaji_pokok')
            ?? data_get($row, 'gaji')
            ?? 0;

        if (is_string($nominalRaw)) {
            $digits  = preg_replace('/[^0-9\-]/', '', $nominalRaw);
            $nominal = $digits === '' ? '0' : $digits;
        } else {
            $nominal = (string) intval(round(floatval($nominalRaw)));
        }
        $rekening = trim((string) $rekening);
        $email    = trim((string) $email);

        return [
            $rekening,  // kolom A (TEXT)
            $nominal,   // kolom B (TEXT)
            $email,     // kolom C (TEXT)
        ];
    }
}
