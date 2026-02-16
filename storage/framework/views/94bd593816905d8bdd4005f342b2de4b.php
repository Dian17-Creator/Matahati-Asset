

<?php
    if (!function_exists('rpOrDash')) {
        function rpOrDash($value)
        {
            if ($value === null || $value === '') {
                return '-';
            }

            // Jika sudah string (misal "Rp 0,00")
            if (is_string($value)) {
                // cek apakah ada angka selain nol
                return preg_match('/[1-9]/', $value) ? $value : '-';
            }

            // Numeric
            return (float) $value === 0.0 ? '-' : 'Rp ' . number_format((float) $value, 2, ',', '.');
        }
    }
?>


<div class="card mt-3 payroll-card" style="margin-bottom: 30px">

    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">Data Penggajian</h4>
    </div>

    <div class="card-body">
        <div class="table-scroll table-sticky">

            <table class="table table-bordered table-striped table-sm table-payroll">
                <thead class="text-center">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">
                            <label class="d-flex align-items-center justify-content-center gap-1 m-0">
                                Pilih
                                <input type="checkbox" id="select_all" style="margin-left:6px;">
                            </label>
                        </th>
                        <th rowspan="2">Nama</th>
                        <th rowspan="2">Jabatan</th>
                        <th rowspan="2">Jumlah Masuk</th>
                        <th rowspan="2">Gaji</th>
                        <th rowspan="2">Gaji Pokok</th>

                        <th colspan="5" class="group-header">Tunjangan</th>

                        <th rowspan="2">Gaji Lembur</th>
                        <th rowspan="2">Tabungan Diambil</th>

                        <th colspan="3" class="group-header">Potongan</th>

                        <th rowspan="2">Total</th>
                        <th rowspan="2">Note</th>
                        <th rowspan="2">Ket Absensi</th>
                        <th rowspan="2">Alasan Edit</th>
                        <th rowspan="2">Action</th>
                    </tr>

                    <tr>
                        <th>Makan</th>
                        <th>Jabatan</th>
                        <th>Transport</th>
                        <th>Luar Kota</th>
                        <th>Masa Kerja</th>

                        <th>Lain</th>
                        <th>Tabungan</th>
                        <th>Keterlambatan</th>
                    </tr>
                </thead>

                <tbody id="tablePayrollBody" class="text-center">
                    <?php
                        $selectedDep = (string) request()->query('department_id', '');
                    ?>

                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $r = is_object($row) ? (array) $row : $row;
                            $rowDep = (string) ($r['department_id'] ?? '');
                        ?>

                        
                        <?php if($selectedDep !== '' && $rowDep !== $selectedDep): ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <tr data-user-id="<?php echo e($r['user_id'] ?? ''); ?>"
                            data-department-id="<?php echo e($r['department_id'] ?? ''); ?>"
                            data-period-month="<?php echo e($month ?? ($selMonth ?? \Carbon\Carbon::now()->month)); ?>"
                            data-period-year="<?php echo e($year ?? ($selYear ?? \Carbon\Carbon::now()->year)); ?>">
                            <td><?php echo e($loop->iteration); ?></td>

                            <td>
                                <input type="checkbox" class="payroll-row-checkbox" value="<?php echo e($r['id']); ?>"
                                    data-name="<?php echo e($r['user_name']); ?>" data-jabatan="<?php echo e($r['jabatan']); ?>"
                                    data-hari="<?php echo e($r['jumlah_masuk']); ?>">
                            </td>

                            <td><?php echo e($r['user_name']); ?></td>
                            <td><?php echo e($r['jabatan']); ?></td>
                            <td><?php echo e($r['jumlah_masuk']); ?></td>

                            <td><?php echo e(rpOrDash($r['gaji'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['gaji_pokok'] ?? null)); ?></td>

                            <td><?php echo e(rpOrDash($r['tunjangan_makan'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['tunjangan_jabatan'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['tunjangan_transport'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['tunjangan_luar_kota'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['tunjangan_masa_kerja'] ?? null)); ?></td>

                            <td><?php echo e(rpOrDash($r['gaji_lembur'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['tabungan_diambil'] ?? null)); ?></td>

                            <td><?php echo e(rpOrDash($r['potongan_lain'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['potongan_tabungan'] ?? null)); ?></td>
                            <td><?php echo e(rpOrDash($r['potongan_keterlambatan'] ?? null)); ?></td>

                            <td class="fw-semibold"><?php echo e(rpOrDash($r['total_gaji'] ?? null)); ?></td>

                            <td><?php echo e($r['note'] ?? '-'); ?></td>

                            <td class="ket-absensi-cell"><?php echo e($r['keterangan_absensi'] ?? 'A = 0, I = 0, S = 0'); ?></td>

                            <td><?php echo e($r['reasonedit'] ?? '-'); ?></td>

                            <td>
                                <button class="btn btn-sm btn-warning btn-open-edit" data-id="<?php echo e(e($r['id'] ?? '')); ?>"
                                    data-user-id="<?php echo e(e($r['user_id'] ?? '')); ?>"
                                    data-jumlah-masuk="<?php echo e(e($r['jumlah_masuk'] ?? 0)); ?>"
                                    data-gaji-harian="<?php echo e(e($r['gaji_harian'] ?? 0)); ?>"
                                    data-gaji-pokok="<?php echo e(e($r['gaji_pokok'] ?? 0)); ?>"
                                    data-tunjangan-makan="<?php echo e(e($r['tunjangan_makan'] ?? 0)); ?>"
                                    data-tunjangan-jabatan="<?php echo e(e($r['tunjangan_jabatan'] ?? 0)); ?>"
                                    data-tunjangan-transport="<?php echo e(e($r['tunjangan_transport'] ?? 0)); ?>"
                                    data-tunjangan-luar-kota="<?php echo e(e($r['tunjangan_luar_kota'] ?? 0)); ?>"
                                    data-tunjangan-masa-kerja="<?php echo e(e($r['tunjangan_masa_kerja'] ?? 0)); ?>"
                                    data-gaji-lembur="<?php echo e(e($r['gaji_lembur'] ?? 0)); ?>"
                                    data-tabungan-diambil="<?php echo e(e($r['tabungan_diambil'] ?? 0)); ?>"
                                    data-potongan-lain="<?php echo e(e($r['potongan_lain'] ?? 0)); ?>"
                                    data-potongan-tabungan="<?php echo e(e($r['potongan_tabungan'] ?? 0)); ?>"
                                    data-potongan-keterlambatan="<?php echo e(e($r['potongan_keterlambatan'] ?? 0)); ?>"
                                    data-note="<?php echo e(e($r['note'] ?? '')); ?>"
                                    data-reasonedit="<?php echo e(e($r['reasonedit'] ?? '')); ?>" data-bs-toggle="modal"
                                    data-bs-target="#modalEditPayroll">
                                    Edit
                                </button>
                            </td>
                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="20" class="text-muted">Data belum tersedia</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

            <?php $__env->startPush('scripts'); ?>
                <script>
                    function applyRecalcResults(results = []) {
                        if (!Array.isArray(results)) return;

                        results.forEach(item => {
                            const userId = item.user_id ?? item.userId ?? item.nuserid;
                            const ket = item.ket ?? item.keterangan_absensi ?? item.keterangan ?? '';
                            if (!userId) return;
                            const row = document.querySelector('tr[data-user-id="' + userId + '"]');
                            if (!row) return;
                            const cell = row.querySelector('.ket-absensi-cell');
                            if (!cell) return;
                            cell.textContent = ket && ket.trim().length ? ket : 'A = 0, I = 0, S = 0';
                        });
                    }
                </script>
            <?php $__env->stopPush(); ?>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/penggajian.js')); ?>"></script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/components/table_payroll.blade.php ENDPATH**/ ?>