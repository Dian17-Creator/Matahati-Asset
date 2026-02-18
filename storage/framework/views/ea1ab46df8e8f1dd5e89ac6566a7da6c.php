<table class="table table-bordered table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($kat->ckode); ?></td>
                <td><?php echo e($kat->cnama); ?></td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori"
                        onclick="openEditKategori(<?php echo e($kat->nid); ?>, '<?php echo e($kat->ckode); ?>', '<?php echo e($kat->cnama); ?>')">
                        Edit
                    </button>

                    <form method="POST" action="<?php echo e(route('asset.kategori.destroy', $kat->nid)); ?>" class="d-inline"
                        onsubmit="return confirm('Hapus kategori ini?')">
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
    <?php echo e($kategori->links('pagination::bootstrap-5')); ?>

</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/partials/kategori_table.blade.php ENDPATH**/ ?>