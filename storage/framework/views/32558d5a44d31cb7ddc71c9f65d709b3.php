<?php
    // === GUARD VARIABLE (WAJIB) ===
    $departments = $departments ?? collect();
    $rekenings = $rekenings ?? collect();
    $devices = $devices ?? collect();
    $admins = $admins ?? collect();
?>

<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Dashboard Backoffice</h2>

            <?php if(auth()->user()->fhrd == 1): ?>
                <button style="background-color: blue; padding: 7px; color: white;" class="btn btn-light btn-sm"
                    data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    + Tambah Departemen
                </button>
            <?php endif; ?>
        </div>
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(auth()->user()->fhrd == 1): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Master Departemen</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($departments)): ?>
                                <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($dept->nid); ?></td>
                                        <td><?php echo e($dept->cname); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editDepartmentModal<?php echo e($dept->nid); ?>">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteDepartmentModal<?php echo e($dept->nid); ?>">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>

                                    
                                    <?php echo $__env->make('backoffice.modal.modal_edit_department', ['dept' => $dept], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php echo $__env->make('backoffice.modal.modal_delete_department', ['dept' => $dept], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-muted">Belum ada departemen</td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-muted">Data departemen tidak tersedia</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="position-relative flex-grow-1 me-3">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                    placeholder="Cari user (nama, email, cabang, role)...">
                <button id="clearSearch" class="btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2"
                    style="border: none; display:none;">✖</button>
            </div>

            <?php if(auth()->user()->fhrd == 1): ?>
                <div class="d-inline-flex gap-2">
                    <button style="background-color: green" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                        + Tambah User
                    </button>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importFingerprintModal"
                        style="color: rgb(39, 39, 39)">
                        Import Fingerprint
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendSlipModal">
                        Kirim Slip Gaji
                    </button>
                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exportUserModal">
                        Export User
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header text-white" style="background-color: green">
                Daftar User
            </div>
            <div class="card-body">
                <div class="table-scroll">
                    <table id="userTable" class="table table-bordered align-middle text-center table-users">
                        <thead class="bg-light">
                            <tr>
                                <th>Username</th>
                                <th>Gmail</th>
                                <th>No Telepon</th>
                                <th>Nomor KTP</th>
                                <th>Nomor Rekening</th>
                                <th>Jenis Bank</th>
                                <th>Nama</th>
                                <th>Nama Lengkap</th>
                                <th>Finger ID</th> 
                                <th>Tanggal Masuk</th>
                                <th class="sortable" data-column="cabang">
                                    Cabang <span class="sort-icon" id="sortIconCabang">↕</span>
                                </th>
                                <th class="sortable" data-column="role">
                                    Role <span class="sort-icon" id="sortIconRole">↕</span>
                                </th>
                                <th>Status</th>
                                <th>Absensi</th>
                                <th>Izin</th>
                                <th>Face</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($users)): ?>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($user->cemail); ?></td>
                                        <td><?php echo e($user->cmailaddress ?? '-'); ?></td>
                                        <td><?php echo e($user->cphone ?? '-'); ?></td>
                                        <td><?php echo e($user->cktp ?? '-'); ?></td>

                                        <td>
                                            <?php echo e($user->caccnumber ? preg_replace('/\s+/', '', $user->caccnumber) : $user->rekening->nomor_rekening ?? '-'); ?>

                                        </td>

                                        <td>
                                            <?php
                                                $bankUser = trim((string) ($user->bank ?? ''));
                                                $bankRel = trim((string) ($user->rekening->bank ?? ''));
                                                $bankName =
                                                    $bankUser !== '' ? $bankUser : ($bankRel !== '' ? $bankRel : '');
                                                $atasNama = trim((string) ($user->rekening->atas_nama ?? ''));
                                            ?>

                                            <?php if($bankName === ''): ?>
                                                -
                                            <?php elseif(strtolower($bankName) === 'mandiri'): ?>
                                                <?php echo e($atasNama !== '' ? 'MANDIRI - ' . $atasNama : 'MANDIRI'); ?>

                                            <?php else: ?>
                                                <?php echo e(strtoupper($bankName)); ?>

                                            <?php endif; ?>
                                        </td>

                                        <td><?php echo e($user->cname); ?></td>
                                        <td><?php echo e($user->cfullname ?? '-'); ?></td>
                                        <td><?php echo e($user->finger_id ?? '-'); ?></td>
                                        <td><?php echo e($user->dtanggalmasuk ? \Carbon\Carbon::parse($user->dtanggalmasuk)->format('d M Y') : '-'); ?>

                                        </td>
                                        <td><?php echo e($user->department->cname ?? '-'); ?></td>

                                        <td>
                                            <?php if($user->fhrd): ?>
                                                <span class="badge bg-info text-dark">HRD</span>
                                            <?php elseif($user->fsuper): ?>
                                                <span class="badge bg-primary">Supervisor</span>
                                            <?php elseif($user->fadmin): ?>
                                                <span class="badge bg-success">Captain</span>
                                            <?php elseif($user->fsenior): ?>
                                                <span class="badge bg-warning text-dark">Senior Crew</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Crew</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($user->isActive()): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo e(route('backoffice.viewLogs', $user->nid)); ?>"
                                                class="btn btn-info btn-sm text-white">Lihat</a>
                                            <?php if(auth()->user()->fhrd == 1): ?>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLogsModal<?php echo e($user->nid); ?>">
                                                    Hapus
                                                </button>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo e(route('backoffice.viewRequests', $user->nid)); ?>"
                                                class="btn btn-info btn-sm text-white">Lihat</a>

                                            <?php if(auth()->user()->fhrd == 1): ?>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLogsModal<?php echo e($user->nid); ?>">
                                                    Hapus
                                                </button>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($user->faces->isNotEmpty()): ?>
                                                <button class="btn btn-outline-primary btn-sm" title="Lihat Face User"
                                                    onclick="window.location.href='<?php echo e(route('hr.face_approval.show', $user->nid)); ?>'">
                                                    Lihat
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Belum</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal<?php echo e($user->nid); ?>">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>

                                    <?php echo $__env->make('backoffice.modal.modal_edit_user', [
                                        'user' => $user,
                                        'departments' => $departments ?? collect(),
                                        'rekenings' => $rekenings ?? collect(),
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                                    <div class="modal fade" id="deleteLogsModal<?php echo e($user->nid); ?>" tabindex="-1"
                                        aria-labelledby="deleteLogsModalLabel<?php echo e($user->nid); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteLogsModalLabel<?php echo e($user->nid); ?>">
                                                        Konfimasi Hapus Logs User <?php echo e($user->cname); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Apakah Anda Ingin Menghapus Data Ini?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <form method="POST" action="<?php echo e(route('backoffice.deleteLogs')); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="user_id" value="<?php echo e($user->nid); ?>">
                                                        <button type="submit" class="btn btn-danger">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="deleteRequestsModal<?php echo e($user->nid); ?>" tabindex="-1"
                                        aria-labelledby="deleteRequestsModalLabel<?php echo e($user->nid); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteRequestsModalLabel<?php echo e($user->nid); ?>">
                                                        Konfimasi Hapus Requests User <?php echo e($user->cname); ?> </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Apakah Anda Ingin Menghapus Data Ini?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <form method="POST" action="<?php echo e(route('backoffice.deleteRequests')); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="user_id" value="<?php echo e($user->nid); ?>">
                                                        <button type="submit" class="btn btn-danger">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="15" class="text-center text-muted">
                                            Belum ada data user
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="15" class="text-center text-muted">
                                        Data user tidak tersedia
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>


                    </table>
                </div>
            </div>
        </div>

        
        <?php echo $__env->make('backoffice.device', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
    
    <?php echo $__env->make('backoffice.modal.modal_send_slip', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('backoffice.modal.modal_tambah_department', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('backoffice.modal.modal_tambah_user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('backoffice.modal.modal_import_fingerprint', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('backoffice.modal.modal_export_user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        th.sortable {
            cursor: pointer;
            user-select: none;
        }

        .sort-icon {
            font-size: 0.8rem;
            margin-left: 4px;
            color: #666;
        }

        th.sortable:hover .sort-icon {
            color: #000;
        }

        #searchInput {
            padding-right: 30px;
        }

        #clearSearch {
            color: #555;
            cursor: pointer;
        }

        #clearSearch:hover {
            color: #000;
        }

        /* === SCROLL & LAYOUT TABEL USER === */
        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-users {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 0;
            /* ⬅️ tidak lagi geser ke kanan */
            min-width: 2000px;
            /* boleh diubah sesuai kebutuhan kolom */
        }

        @media (max-width: 768px) {

            .table-users th,
            .table-users td {
                white-space: nowrap;
                font-size: 11px;
                padding: 4px 6px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== Deklarasi elemen utama =====
            const table = document.getElementById('userTable');
            const tbody = table ? table.querySelector('tbody') : null;
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            // ===== Fungsi Search =====
            if (searchInput && tbody) {
                searchInput.addEventListener('input', function() {
                    const keyword = this.value.toLowerCase();
                    const rows = tbody.querySelectorAll('tr');
                    clearSearch.style.display = keyword ? 'block' : 'none';

                    rows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });

                // Reset Search
                clearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    this.style.display = 'none';
                    tbody.querySelectorAll('tr').forEach(row => row.style.display = '');
                });
            }

            // ===== Fungsi Sorting =====
            if (tbody) {
                let sortState = {
                    column: null,
                    direction: 'asc'
                };

                function sortTable(columnIndex, keyExtractor) {
                    const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.cells.length > 0);
                    const currentDir = sortState.direction;

                    rows.sort((a, b) => {
                        const aText = keyExtractor(a);
                        const bText = keyExtractor(b);
                        return currentDir === 'asc' ?
                            aText.localeCompare(bText) :
                            bText.localeCompare(aText);
                    });

                    tbody.innerHTML = '';
                    rows.forEach(r => tbody.appendChild(r));
                    sortState.direction = currentDir === 'asc' ? 'desc' : 'asc';
                }

                document.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', () => {
                        const column = header.dataset.column;
                        document.querySelectorAll('.sort-icon').forEach(icon => icon.textContent =
                            '↕');

                        if (column === 'cabang') {
                            sortTable(5, row => row.cells[5].innerText.trim());
                            document.getElementById('sortIconCabang').textContent =
                                sortState.direction === 'asc' ? '▲' : '▼';
                        }

                        if (column === 'role') {
                            sortTable(6, row => row.cells[6].innerText.trim());
                            document.getElementById('sortIconRole').textContent =
                                sortState.direction === 'asc' ? '▲' : '▼';
                        }
                    });
                });
            }
        });
    </script>

    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 12000;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function showToast(message, variant = 'primary') {
                const container = document.getElementById('toastContainer');
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${variant} border-0 mb-2`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body fw-semibold">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
                container.appendChild(toastEl);
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 6000
                });
                toast.show();
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            }

            <?php if(session('slip_status')): ?>
                showToast("<?php echo e(addslashes(session('slip_status'))); ?>", 'success');
            <?php endif; ?>
            <?php if(session('slip_error')): ?>
                showToast("<?php echo e(addslashes(session('slip_error'))); ?>", 'danger');
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/index.blade.php ENDPATH**/ ?>