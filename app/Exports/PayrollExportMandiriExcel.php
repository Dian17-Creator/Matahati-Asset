<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PayrollExportMandiriExcel implements FromArray, WithEvents, WithTitle
{
    protected $data;
    protected $companyAccount;
    protected $companyAlias;
    protected $reference;
    protected $dateYmd;   // final string 'YYYYMMDD'
    protected $routeUrl;
    protected $payrollDay;
    protected $totalAmount = 0;
    protected $countRows = 0;

    public function title(): string
    {
        return 'Converter'; // nama sheet 1
    }

    /**
     * $dateYmd optional: can be null | Carbon | 'YYYYMMDD' string | other parsable date string
     * $payrollDay default 5
     */
    public function __construct(
        $data,
        $companyAccount = '1710007401451',
        $companyAlias = 'BANK MANDIRI - ANDRE TUWAN',
        $reference = '15786538',
        $dateYmd = null,
        $routeUrl = null,
        $payrollDay = 5,
        $periodYear = null,    // NEW
        $periodMonth = null    // NEW
    ) {
        $this->data = $data;
        $this->companyAccount = $companyAccount;
        $this->companyAlias = $companyAlias;
        $this->reference = $reference;
        $this->routeUrl = $routeUrl;
        $this->payrollDay = (int)$payrollDay;

        // If caller passed a real YYYYMMDD string, accept it.
        // Else try to parse/normalize; if still not possible, use provided periodYear/periodMonth + payrollDay; fallback to now.
        if ($dateYmd) {
            if ($dateYmd instanceof Carbon) {
                $dt = $dateYmd;
            } else {
                $s = trim((string)$dateYmd);
                if (preg_match('/^\d{8}$/', $s)) {
                    // string already YYYYMMDD -> parse
                    $dt = Carbon::createFromFormat('Ymd', $s);
                } else {
                    // try parse
                    try {
                        $dt = Carbon::parse($s);
                    } catch (\Exception $e) {
                        $dt = null;
                    }
                }
            }

            if ($dt) {
                // ensure day doesn't exceed month; if caller passed a date we keep its year/month but force day->payrollDay if needed
                $lastDay = $dt->copy()->endOfMonth()->day;
                $day = min($this->payrollDay, $lastDay);
                $dt = Carbon::create($dt->year, $dt->month, $day);
                $this->dateYmd = $dt->format('Ymd');
            } else {
                $this->dateYmd = null;
            }
        } else {
            $this->dateYmd = null;
        }

        // If still null, try to build from provided periodYear & periodMonth
        if (empty($this->dateYmd)) {
            if ($periodYear && $periodMonth) {
                $lastDay = Carbon::create($periodYear, $periodMonth, 1)->endOfMonth()->day;
                $day = min($this->payrollDay, $lastDay);
                $dt = Carbon::create($periodYear, $periodMonth, $day);
                $this->dateYmd = $dt->format('Ymd');
            } else {
                // fallback: now's month
                $now = Carbon::now();
                $lastDay = $now->copy()->endOfMonth()->day;
                $day = min($this->payrollDay, $lastDay);
                $this->dateYmd = Carbon::create($now->year, $now->month, $day)->format('Ymd');
            }
        }
    }

    public function array(): array
    {
        $rowsCollection = $this->data instanceof \Illuminate\Support\Collection
            ? $this->data
            : collect($this->data);

        $filtered = $rowsCollection->filter(function ($row) {
            $acc = $row->user?->caccnumber ?? ($row->caccnumber ?? null);
            return !empty($acc);
        })->map(function ($row) {
            $amount = $row->total_gaji ?? 0;
            if (!is_numeric($amount)) {
                $clean = preg_replace('/[^0-9\-]/', '', (string)$amount);
                $amount = $clean === '' ? 0 : (int)$clean;
            } else {
                $amount = (int)$amount;
            }

            return (object)[
                'user_id'     => $row->user?->id ?? $row->user_id ?? null,
                'caccnumber'  => $row->user?->caccnumber ?? ($row->caccnumber ?? ''),
                'cfullname'   => $row->user?->cfullname ?? ($row->user?->cname ?? ($row->cfullname ?? '')),
                'amount'      => $amount,
                'email'       => $row->user?->email ?? ($row->user?->cmailaddress ?? ''),
                'nip'         => $row->user?->nip ?? '',
            ];
        });

        $unique = $filtered->unique(function ($r) {
            return $r->caccnumber ?: $r->user_id;
        })->values();

        $this->countRows = $unique->count();
        $this->totalAmount = $unique->sum('amount');

        $rows = [];
        $rows[] = ['Batch Upload MCM 2.0'];         // A1
        $rows[] = ['Harus / Mandatory'];           // A2
        $rows[] = ['Pilihan / Optional'];          // A3
        $rows[] = [''];                            // A4
        $rows[] = [$this->companyAlias];           // A5

        // B6 is dateYmd string, C6 companyAccount, D6 count, E6 total, F6 reference
        $rows[] = [
            'P',
            $this->dateYmd,
            $this->companyAccount,
            $this->countRows,
            $this->totalAmount,
            $this->reference,
        ];

        foreach ($unique as $row) {
            $rows[] = [
                $row->caccnumber,    // A
                $row->cfullname,     // B
                '', '', '',
                'IDR',               // F
                $row->amount,        // G
                'Gaji',              // H
                '',                  // I
                'IBU',               // J
                '',                  // K
                'MANDIRI',           // L
                'Tulungagung',       // M
                '',                  // N
                'N',                 // O
                '',                  // P
                'OUR',               // Q
                '1',                 // R
                'E'                  // S
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'underline' => true],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']],
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFFF00']],
                ]);

                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0000FF']],
                ]);

                // write date and company account as strings to avoid excel auto-formatting
                $sheet->setCellValueExplicit('B6', $this->dateYmd, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C6', $this->companyAccount, DataType::TYPE_STRING);

                $sheet->setCellValue('D6', (int)($this->countRows ?? 0));
                $sheet->setCellValue('E6', (int)($this->totalAmount ?? 0));
                $sheet->getStyle('E6')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->setCellValueExplicit('F6', $this->reference, DataType::TYPE_STRING);

                $sheet->getStyle('A6:F6')->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFFF00']],
                    'font' => ['bold' => true],
                ]);

                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("J7:J{$lastRow}")->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFFF00']],
                ]);

                $sheet->getStyle("Q7:S{$lastRow}")->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFFF00']],
                ]);

                foreach (range('A', 'S') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                if ($this->routeUrl) {
                    $sheet->setCellValue('B2', '=HYPERLINK("'.$this->routeUrl.'", "Convert CSV")');

                    $sheet->getStyle('B2')->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'C0C0C0']],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                $sheet->getStyle("G7:G{$lastRow}")
                      ->getNumberFormat()
                      ->setFormatCode('#,##0');
            }
        ];
    }
}
