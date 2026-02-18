<table class="table table-bordered table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($satuan->firstItem() + $i); ?></td>
                <td><?php echo e($s->nama); ?></td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSatuan"
                        onclick="openEditSatuan(<?php echo e($s->id); ?>, '<?php echo e($s->nama); ?>')">
                        Edit
                    </button>

                    <form method="POST" action="<?php echo e(route('msatuan.destroy', $s->id)); ?>" class="d-inline"
                        onsubmit="return confirm('Hapus satuan ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<div class="d-flex justify-content-center">
    <?php echo e($satuan->links('pagination::bootstrap-5')); ?>

</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/partials/satuan_table.blade.php ENDPATH**/ ?>