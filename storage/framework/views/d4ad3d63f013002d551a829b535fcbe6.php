

<div class="card mt-3 payroll-card" style="margin-bottom: 20px">

    <div class="card-header d-flex justify-content-between align-items-center bg-danger text-white">
        <h5 class="mb-0">Master Rekening</h5>
    </div>

    <div class="card-body p-2">
        <div class="table-scroll">
            <table class="table table-bordered table-sm">
                <thead class="text-center" style="background:#d7ffe2">
                    <tr>
                        <th>No</th>
                        <th>No Rekening</th>
                        <th>Bank</th>
                        <th>Atas Nama</th>
                        <th>Cabang</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    <?php $__empty_1 = true; $__currentLoopData = $mrekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($r->nomor_rekening); ?></td>
                            <td><?php echo e($r->bank); ?></td>
                            <td><?php echo e($r->atas_nama); ?></td>
                            <td><?php echo e($r->cabang); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit-rekening" data-id="<?php echo e($r->id); ?>"
                                    data-nomor="<?php echo e($r->nomor_rekening); ?>" data-bank="<?php echo e($r->bank); ?>"
                                    data-atas="<?php echo e($r->atas_nama); ?>" data-cabang="<?php echo e($r->cabang); ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEditRekening">
                                    Edit
                                </button>

                                <form action="<?php echo e(route('mrekening.destroy', $r->id)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus rekening ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-muted">Belum ada data rekening</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php /**PATH D:\Matahati-Asset\resources\views/penggajian/components/table_rekening.blade.php ENDPATH**/ ?>