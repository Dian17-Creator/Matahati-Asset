<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MscanReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start ?? date('Y-m-01');
        $this->end = $end ?? date('Y-m-d');
    }

    public function collection()
    {
        $user = Auth::user();
        $isHRD = $user->fhrd == 1;
        $deptId = $user->niddept;

        $query = "
            WITH absen AS (
                SELECT
                    scan.nid,
                    scan.nuserid,
                    scan.dscanned,
                    sched.dstart,
                    sched.dend,
                    sched.cschedname,
                    muser.cname,
                    muser.niddept
                FROM (
                    SELECT nid, nuserid, dscanned FROM mscan
                    UNION ALL
                    SELECT nid, nuserid, dscanned FROM mscan_manual
                    UNION ALL
                    SELECT nid, nuserId AS nuserid, dscanned FROM mface_scan
                ) scan
                LEFT JOIN tuserschedule sched
                    ON sched.nuserid = scan.nuserid
                    AND DATE(scan.dscanned) = DATE(sched.dwork)
                LEFT JOIN muser ON scan.nuserid = muser.nid
                WHERE DATE(scan.dscanned) BETWEEN ? AND ?
            )
            SELECT
                cname,
                DATE(dscanned) AS date,
                cschedname,
                TIME(MIN(dstart)) AS dstart,
                TIME(MAX(dend)) AS dend,
                TIME(MIN(dscanned)) AS in_time,
                TIME(MAX(dscanned)) AS out_time,
                niddept
            FROM absen
        ";

        $bindings = [$this->start, $this->end];

        // Filter berdasarkan role
        if (!$isHRD && $deptId) {
            $query .= " WHERE niddept = ? ";
            $bindings[] = $deptId;
        } elseif (!$isHRD && !$deptId) {
            $query .= " WHERE nuserid = ? ";
            $bindings[] = $user->nid;
        }

        $query .= "
            GROUP BY cname, DATE(dscanned), cschedname, niddept
            ORDER BY cname, DATE(dscanned)
        ";

        $data = collect(DB::select($query, $bindings));

        // Hitung keterlambatan (menit)
        foreach ($data as $row) {
            $dstartSec = strtotime($row->dstart ?? '00:00:00');
            $inSec = strtotime($row->in_time ?? '00:00:00');
            $row->late_minutes = 0;

            if ($inSec > $dstartSec && $row->dstart) {
                $diff = floor(($inSec - $dstartSec) / 60);
                $row->late_minutes = $diff;
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Shift',
            'Jam Masuk (Jadwal)',
            'Jam Checkin',
            'Jam Keluar (Jadwal)',
            'Jam Checkout',
            'Keterlambatan (Menit)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->cname,
            $row->date,
            $row->cschedname ?? '-',
            $row->dstart ?? '-',
            $row->in_time ?? '-',
            $row->dend ?? '-',
            $row->out_time ?? '-',
            $row->late_minutes > 0 ? $row->late_minutes . ' menit' : '-',
        ];
    }
}
