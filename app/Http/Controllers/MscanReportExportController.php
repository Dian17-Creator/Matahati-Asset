<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\MscanReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MscanReportExportController extends Controller
{
    public function export(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $user = Auth::user();

        $userName = strtolower(str_replace(' ', '_', $user->cname));
        $date = date('Ymd');
        $fileName = "laporan_absensi_{$date}.xlsx";

        return Excel::download(new MscanReportExport($start, $end), $fileName);
    }
}
