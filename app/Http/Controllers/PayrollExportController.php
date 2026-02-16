<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Csalary;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExportMandiriExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollExportController extends Controller
{
    // Excel export (creates the .xlsx)
    public function exportMandiriExcel(Request $req)
    {
        // periode dalam format YYYY-MM
        $period = $req->input('period') ?: Carbon::now()->format('Y-m');
        [$year, $month] = explode('-', $period);
        $month = (int) $month;
        $year = (int) $year;

        // Ambil data csalary sesuai periode (PASTIKAN sama untuk Excel & CSV)
        $data = Csalary::where('period_year', $year)
            ->where('period_month', $month)
            ->with('user')
            ->get();

        // build CSV route url for the hyperlink
        $routeUrl = route('payroll.mandiri.csv', ['period' => $period]);

        $dateYmd = Carbon::now()->format('Ymd');

        $export = new PayrollExportMandiriExcel($data, '1710007401451', '15786538', $dateYmd, $routeUrl);

        $filename = "mandiri_payroll_{$period}.xlsx";
        return Excel::download($export, $filename);
    }

    // CSV export (Mandiri format). This returns a CSV for immediate download.
    public function exportMandiriCsv(Request $req)
    {
        $period = $req->input('period') ?? Carbon::now()->format('Y-m');
        [$year, $month] = explode('-', $period);
        $month = (int)$month;
        $year = (int)$year;

        // gunakan data yang sama seperti Excel (with user)
        $data = Csalary::where('period_year', $year)
            ->where('period_month', $month)
            ->with('user')
            ->get();

        $companyAccount = '1710007401451';
        $reference = '15786538';
        $dateYmd = Carbon::now()->format('Ymd');

        $filename = "mandiri_payroll_{$dateYmd}.csv";

        $response = new StreamedResponse(function () use ($data, $companyAccount, $reference, $dateYmd) {
            $handle = fopen('php://output', 'w');

            // write P row (company header) - pad to some columns if needed by receiver
            $pRow = ['P', $dateYmd, $companyAccount, '14', $reference];
            fputcsv($handle, $pRow);

            // write each employee row — same mapping as excel array()
            foreach ($data as $row) {
                $acc = (string) ($row->user?->caccnumber ?? '');
                $name = $row->user?->cname ?? '';
                $amount = (int) ($row->total_gaji ?? 0);
                $remark = $row->note ?? 'Gaji';

                $csvRow = [
                    $acc,           // A
                    $name,          // B
                    '', '', '',     // C–E: kosong
                    'IDR',          // F
                    $amount,        // G
                    $remark,        // H
                    '',             // I
                    'IBU',          // J
                    '',             // K
                    'MANDIRI',      // L
                    'Tulungagung',  // M
                    '',             // N
                    'N',            // O
                    '',             // P
                    'OUR',          // Q
                    '1',            // R
                    'E',            // S
                ];

                fputcsv($handle, $csvRow);
            }

            fclose($handle);
        });

        // set headers
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

        return $response;
    }
}
