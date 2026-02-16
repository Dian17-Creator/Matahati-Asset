

<?php
    function rupiahOrDash($value)
    {
        if ($value === null || (float) $value == 0) {
            return '-';
        }
        return 'Rp ' . number_format((float) $value, 2, ',', '.');
    }
?>

<div class="card mt-3 payroll-card" style="margin-bottom: 20px">

    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Gaji dan Tunjangan</h5>
    </div>

    <div class="card-body p-2">
        <div class="table-scroll">
            <table class="table table-bordered table-sm">
                <thead class="text-center" style="background:#d7ebff">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tgl Berlaku</th>
                        <th>Jenis Gaji</th>
                        <th>Nominal</th>
                        <th>Makan</th>
                        <th>Jabatan</th>
                        <th>Transport</th>
                        <th>Luar Kota</th>
                        <th>Masa Kerja</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    <?php $__empty_1 = true; $__currentLoopData = $mtunjangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($t->user->cname ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($t->tanggal_berlaku)->format('d-m-Y')); ?></td>
                            <td><?php echo e($t->jenis_gaji); ?></td>
                            
                            <td><?php echo e(rupiahOrDash($t->nominal_gaji)); ?></td>
                            <td><?php echo e(rupiahOrDash($t->tunjangan_makan)); ?></td>
                            <td><?php echo e(rupiahOrDash($t->tunjangan_jabatan)); ?></td>
                            <td><?php echo e(rupiahOrDash($t->tunjangan_transport)); ?></td>
                            <td><?php echo e(rupiahOrDash($t->tunjangan_luar_kota)); ?></td>
                            <td><?php echo e(rupiahOrDash($t->tunjangan_masa_kerja)); ?></td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-muted">Belum ada data tunjangan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
<?php /**PATH D:\Matahati-Asset\resources\views/penggajian/components/table_tunjangan.blade.php ENDPATH**/ ?>