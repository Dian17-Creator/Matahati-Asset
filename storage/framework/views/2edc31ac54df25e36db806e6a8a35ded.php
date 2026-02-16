
<?php if(auth()->user()->fhrd == 1): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span>Manajemen Device Admin</span>
        </div>

        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Admin</th>
                        <th>Device ID</th>
                        <th>Approval</th>
                        <th>Status</th>
                        <th>Terakhir Digunakan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($device->id); ?></td>
                            <td><?php echo e($device->user->cname ?? '-'); ?></td>

                            <td class="text-break" style="max-width:220px">
                                <?php echo e($device->device_id); ?>

                            </td>

                            
                            <td>
                                <?php if($device->approval_status === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif($device->approval_status === 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <?php if($device->is_active): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo e($device->last_used_at?->format('d M Y H:i') ?? '-'); ?>

                            </td>

                            
                            <td class="d-flex justify-content-center gap-1 flex-wrap">

                                
                                <?php if($device->approval_status === 'pending'): ?>
                                    <form action="<?php echo e(route('admin-devices.approve', $device->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-success btn-sm">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="<?php echo e(route('admin-devices.reject', $device->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-danger btn-sm">
                                            Reject
                                        </button>
                                    </form>
                                <?php endif; ?>

                                
                                <?php if($device->approval_status === 'approved'): ?>
                                    <form action="<?php echo e(route('admin-devices.toggle', $device->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-warning btn-sm">
                                            <?php echo e($device->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>

                                
                                <form action="<?php echo e(route('admin-devices.destroy', $device->id)); ?>" method="POST"
                                    onsubmit="return confirm('Yakin hapus device ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-outline-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-muted">
                                Belum ada device terdaftar
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\Matahati-Asset\resources\views/backoffice/device.blade.php ENDPATH**/ ?>