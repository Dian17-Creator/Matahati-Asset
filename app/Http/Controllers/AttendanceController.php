<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.index');
    }
    public function getAttendanceReport(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Attendance access denied: not authenticated');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $deptId = $user->niddept;
        $isHRD   = $user->fhrd == 1;
        $isAdmin = $user->fadmin == 1;
        $isSuper = $user->fsuper == 1;

        $startDate = $request->query('start');
        $endDate   = $request->query('end');

        Log::info('📋 Attendance Report Request', [
            'user_id' => $user->nid,
            'user_name' => $user->cname,
            'dept_id' => $deptId,
            'fadmin' => $user->fadmin,
            'fsuper' => $user->fsuper,
            'fhrd' => $user->fhrd,
        ]);

        try {
            // build date filter BUT do not append its params to $params yet
            $dateFilter = '';
            $dateParams = [];

            if (!empty($startDate) && !empty($endDate)) {
                $dateFilter = "DATE(scan.dscanned) BETWEEN ? AND ?";
                $dateParams = [$startDate, $endDate];
            } elseif (!empty($startDate)) {
                $dateFilter = "DATE(scan.dscanned) >= ?";
                $dateParams = [$startDate];
            } elseif (!empty($endDate)) {
                $dateFilter = "DATE(scan.dscanned) <= ?";
                $dateParams = [$endDate];
            }

            // base query (kehadiran) — include mface_scan
            $baseQuery = "
            WITH absen AS (
                SELECT
                    scan.nid,
                    scan.nuserid,
                    scan.dscanned,
                    scan.nlat,
                    scan.nlng,
                    scan.nadminid,
                    scan.creason,
                    sched.dstart,
                    sched.dend,
                    sched.cschedname,
                    muser.cname,
                    muser.niddept
                FROM
                    (
                        SELECT nid, nuserid, dscanned, nlat, nlng, nadminid, creason FROM mscan
                        UNION ALL
                        SELECT nid, nuserid, dscanned, nlat, nlng, nadminid, creason FROM mscan_manual
                        UNION ALL
                        SELECT nid, nuserId AS nuserid, dscanned, nlat, nlng, NULL AS nadminid, NULL AS creason FROM mface_scan
                    ) scan
                LEFT JOIN tuserschedule sched
                    ON sched.nuserid = scan.nuserid
                    AND DATE(scan.dscanned) = DATE(sched.dwork)
                LEFT JOIN muser
                    ON scan.nuserid = muser.nid
                /*** FILTER DITAMBAHKAN DI SINI ***/
            )
            SELECT
                nuserid AS user_id,
                DATE(dscanned) AS date,
                TIME(COALESCE(
                    MAX(CASE WHEN TIME(dscanned) <= dstart THEN dscanned END),
                    MIN(dscanned)
                )) AS in_time,
                TIME(COALESCE(
                    MIN(CASE WHEN TIME(dscanned) >= dend THEN dscanned END),
                    MAX(dscanned)
                )) AS out_time,
                dstart, dend, cschedname, cname, niddept
            FROM absen
            GROUP BY nuserid, DATE(dscanned)
            ORDER BY nuserid, DATE(dscanned);
        ";

            // build filters and params in consistent order
            $filters = [];
            $params = [];

            if ($isHRD) {
                Log::info('🔍 HRD mode: semua departemen');
                // HRD sees all; only apply date filter (if any)
                if ($dateFilter) {
                    $filters[] = $dateFilter;
                    $params = array_merge($params, $dateParams);
                }
            } elseif (!empty($deptId)) {
                Log::info('🔍 Filter berdasarkan departemen', ['dept_id' => $deptId]);
                // department filter first, then date filter (if any)
                $filters[] = "muser.niddept = ?";
                $params[] = $deptId;

                if ($dateFilter) {
                    $filters[] = $dateFilter;
                    $params = array_merge($params, $dateParams);
                }
            } else {
                Log::info('🔍 User tanpa departemen, tampilkan data user sendiri', ['user_id' => $user->nid]);
                $filters[] = "muser.nid = ?";
                $params[] = $user->nid;

                if ($dateFilter) {
                    $filters[] = $dateFilter;
                    $params = array_merge($params, $dateParams);
                }
            }

            $whereClause = '';
            if (!empty($filters)) {
                $whereClause = "WHERE " . implode(" AND ", $filters);
            }

            $finalQuery = str_replace('/*** FILTER DITAMBAHKAN DI SINI ***/', $whereClause, $baseQuery);

            Log::info('🧮 Final Query', ['query' => $finalQuery, 'params' => $params]);

            $attendance = DB::select($finalQuery, $params);

            Log::info('✅ Attendance Query Success', ['total_records' => count($attendance)]);

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'total' => count($attendance)
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Attendance Query Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance data: ' . $e->getMessage()
            ], 500);
        }
    }

}
