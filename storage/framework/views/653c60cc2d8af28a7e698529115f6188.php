<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <h3 style="margin-bottom: 15px">Atur Jadwal untuk <?php echo e($user->cname); ?></h3>

        <form action="<?php echo e(route('schedule.assign')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="nuserid" value="<?php echo e($user->nid); ?>">

            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $period; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $d = $date->format('Y-m-d'); ?>
                        <tr>
                            <td><?php echo e($date->format('d/m/Y')); ?></td>
                            <td>
                                <select name="dates[<?php echo e($d); ?>]" class="form-select">
                                    <option value="">-- none --</option>
                                    <?php $__currentLoopData = $masters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($m->nid); ?>"
                                            <?php if(isset($existingSchedules[$d]) && $existingSchedules[$d] == $m->nid): ?> selected <?php endif; ?>>
                                            <?php echo e($m->cname); ?>

                                            (<?php echo e(substr($m->dstart, 0, 5)); ?> - <?php echo e(substr($m->dend, 0, 5)); ?>

                                            <?php if($m->dstart2 && $m->dend2): ?>
                                                | <?php echo e(substr($m->dstart2, 0, 5)); ?> - <?php echo e(substr($m->dend2, 0, 5)); ?>

                                            <?php endif; ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Simpan Jadwal</button>
            <a href="<?php echo e(route('schedule.index')); ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/schedule/assign.blade.php ENDPATH**/ ?>