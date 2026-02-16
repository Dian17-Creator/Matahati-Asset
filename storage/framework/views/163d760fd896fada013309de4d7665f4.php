<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="#" id="exportBtn" class="btn btn-success fw-semibold">
                <i class="fas fa-file-excel me-1"></i> Export Laporan
            </a>
        </div>

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Laporan Absensi
                </h4>

                <div class="d-flex align-items-center gap-2">
                    <input type="date" id="startDate" class="form-control form-control-sm" style="max-width: 180px;">
                    <span>to</span>
                    <input type="date" id="endDate" class="form-control form-control-sm" style="max-width: 180px;">
                    <button class="btn btn-light text-dark fw-semibold" onclick="loadAttendance()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>

            </div>

            <div class="card-body">
                
                <div id="loading" class="loading text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>

                
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>

                
                <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jadwal/Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Checkin</th>
                                <th>Jam Keluar</th>
                                <th>Jam Checkout</th>
                                <th>Keterlambatan (Menit)</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Klik <b>"Refresh"</b> untuk memuat data absensi
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                
                <div id="paginationControls" class="d-flex justify-content-between align-items-center mt-3 d-none">
                    <button id="prevBtn" class="btn btn-outline-secondary btn-sm" disabled>← Prev</button>
                    <span id="pageInfo" class="fw-semibold"></span>
                    <button id="nextBtn" class="btn btn-outline-secondary btn-sm">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .loading {
            display: none;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table thead th {
            top: 0;
            background-color: #212529 !important;
            color: #fff;
            z-index: 10;
        }

        .text-late {
            color: #dc3545 !important;
            font-weight: bold;
        }

        .btn-light:hover {
            background-color: #f8f9fa;
            color: #000;
        }

        .card.shadow-sm {
            margin-bottom: 30px !important;
        }
    </style>

    <script>
        let attendanceData = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        function loadAttendance() {
            const loadingElement = document.getElementById('loading');
            const tableBody = document.getElementById('attendanceTableBody');
            const errorAlert = document.getElementById('errorAlert');
            const pagination = document.getElementById('paginationControls');

            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            currentPage = 1;
            loadingElement.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            pagination.classList.add('d-none');
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';

            let url = '<?php echo e(route('attendance.report')); ?>';
            const params = new URLSearchParams();
            if (startDate) params.append('start', startDate);
            if (endDate) params.append('end', endDate);
            if (params.toString()) url += '?' + params.toString();

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    loadingElement.classList.add('d-none');

                    if (data.success) {
                        attendanceData = data.data;
                        if (attendanceData.length > 0) {
                            pagination.classList.remove('d-none');
                        }
                        displayAttendancePage();
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    loadingElement.classList.add('d-none');
                    errorAlert.textContent = 'Error: ' + error.message;
                    errorAlert.classList.remove('d-none');
                    tableBody.innerHTML =
                        '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>';
                });
        }

        function displayAttendancePage() {
            const tableBody = document.getElementById('attendanceTableBody');
            const pagination = document.getElementById('paginationControls');
            const pageInfo = document.getElementById('pageInfo');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = attendanceData.slice(start, end);

            if (attendanceData.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data absensi</td></tr>';
                pagination.classList.add('d-none');
                return;
            }

            let html = '';
            paginatedData.forEach(record => {
                const inTime = record.in_time || '';
                const outTime = record.out_time || '';
                const dstart = record.dstart || '';
                const dend = record.dend || '';
                const cname = record.cname || '';
                const date = record.date || '';
                const cschedname = record.cschedname || '';

                const toSeconds = t => {
                    if (!t) return 0;
                    const [h, m, s] = t.trim().split(':').map(Number);
                    return h * 3600 + m * 60 + s;
                };

                const dstartSec = toSeconds(dstart);
                let dendSec = toSeconds(dend);
                let inSec = toSeconds(inTime);
                let outSec = toSeconds(outTime);

                if (dendSec < dstartSec) {
                    dendSec += 24 * 3600;
                    if (outSec < dstartSec) outSec += 24 * 3600;
                }

                const isCheckInLate = inTime && dstart && inSec > dstartSec;
                const isCheckOutEarly = outTime && dend && outSec < dendSec;

                // hitung keterlambatan dalam menit
                let lateMinutes = 0;
                if (isCheckInLate) {
                    lateMinutes = Math.floor((inSec - dstartSec) / 60);
                }

                html += `
                    <tr>
                        <td>${cname}</td>
                        <td>${date}</td>
                        <td>${cschedname}</td>
                        <td>${dstart}</td>
                        <td class="${isCheckInLate ? 'text-late' : ''}">${inTime}</td>
                        <td>${dend}</td>
                        <td class="${isCheckOutEarly ? 'text-late' : ''}">${outTime}</td>
                        <td class="${lateMinutes > 0 ? 'text-late' : ''}">${lateMinutes > 0 ? lateMinutes + ' menit' : '-'}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;

            const totalPages = Math.ceil(attendanceData.length / rowsPerPage);
            pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayAttendancePage();
                }
            };
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayAttendancePage();
                }
            };
        }

        document.addEventListener('DOMContentLoaded', loadAttendance);
    </script>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);

            const url = '<?php echo e(route('export-attendance-report')); ?>' + (params.toString() ? '?' + params.toString() :
                '');
            window.location.href = url;
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Matahati-Asset\resources\views/attendance/index.blade.php ENDPATH**/ ?>