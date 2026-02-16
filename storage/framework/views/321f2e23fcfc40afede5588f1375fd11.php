
<?php
    if (empty($data) || !is_array($data) || count($data) === 0) {
        echo '<tr><td colspan="999" class="text-muted">Data belum tersedia</td></tr>';
        return;
    }
?>

<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $r = is_object($row) ? (array) $row : (array) $row;
        $userId = $r['user_id'] ?? '';
        $ketDisplay = $r['keterangan_absensi'] ?? 'A = 0, I = 0, S = 0';
    ?>

    <tr data-user-id="<?php echo e(e($userId)); ?>" data-department-id="<?php echo e(e($r['department_id'] ?? '')); ?>">
        <td><?php echo e($loop->iteration); ?></td>
        <td>
            <input type="checkbox" class="payroll-row-checkbox" value="<?php echo e(e($r['id'] ?? '')); ?>"
                data-name="<?php echo e(e($r['user_name'] ?? '')); ?>" data-jabatan="<?php echo e(e($r['jabatan'] ?? '')); ?>"
                data-hari="<?php echo e(e($r['jumlah_masuk'] ?? 0)); ?>">
        </td>
        <td><?php echo e(e($r['user_name'] ?? '-')); ?></td>
        <td><?php echo e(e($r['jabatan'] ?? '-')); ?></td>
        <td class="text-center"><?php echo e(e($r['jumlah_masuk'] ?? 0)); ?></td>
        <td class="text-end"><?php echo e(e($r['gaji'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['gaji_pokok'] ?? 'Rp 0')); ?></td>

        <td class="text-end"><?php echo e(e($r['tunjangan_makan'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['tunjangan_jabatan'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['tunjangan_transport'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['tunjangan_luar_kota'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['tunjangan_masa_kerja'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['gaji_lembur'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['tabungan_diambil'] ?? 'Rp 0')); ?></td>

        <td class="text-end"><?php echo e(e($r['potongan_lain'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['potongan_tabungan'] ?? 'Rp 0')); ?></td>
        <td class="text-end"><?php echo e(e($r['potongan_keterlambatan'] ?? 'Rp 0')); ?></td>

        <td class="text-end"><?php echo e(e($r['total_gaji'] ?? 'Rp 0')); ?></td>
        <td><?php echo e(e($r['note'] ?? '-')); ?></td>

        <td class="ket-absensi-cell"><?php echo e(e($ketDisplay)); ?></td>

        <td><?php echo e(e($r['reasonedit'] ?? '-')); ?></td>

        <td>
            <button class="btn btn-sm btn-warning btn-open-edit" data-id="<?php echo e(e($r['id'] ?? '')); ?>"
                data-user-id="<?php echo e(e($r['user_id'] ?? '')); ?>" data-jumlah-masuk="<?php echo e(e($r['jumlah_masuk'] ?? 0)); ?>"
                data-gaji-harian="<?php echo e(e($r['gaji_harian'] ?? 0)); ?>" data-gaji-pokok="<?php echo e(e($r['gaji_pokok'] ?? 0)); ?>"
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
                data-note="<?php echo e(e($r['note'] ?? '')); ?>" data-reasonedit="<?php echo e(e($r['reasonedit'] ?? '')); ?>"
                data-bs-toggle="modal" data-bs-target="#modalEditPayroll">
                Edit
            </button>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/components/table_payroll_rows.blade.php ENDPATH**/ ?>