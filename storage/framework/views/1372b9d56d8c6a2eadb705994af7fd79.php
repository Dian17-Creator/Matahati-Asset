<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Sub Kategori</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori"
            onclick="openCreateSubKategori()">
            + Tambah Sub Kategori
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-sm align-middle">
            <thead class="text-center">
                <tr>
                    <th>Kategori</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $subkategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($sub->kategori->cnama); ?></td>
                        <td class="text-center"><?php echo e($sub->ckode); ?></td>
                        <td><?php echo e($sub->cnama); ?></td>
                        <td class="text-center">
                            <?php if($sub->fqr): ?>
                                <span class="badge bg-primary">QR</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non QR</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalSubKategori"
                                onclick="openEditSubKategori(
                                        <?php echo e($sub->nid); ?>,
                                        <?php echo e($sub->nidkat); ?>,
                                        '<?php echo e($sub->ckode); ?>',
                                        '<?php echo e($sub->cnama); ?>',
                                        <?php echo e($sub->fqr ? 1 : 0); ?>

                                    )">
                                Edit
                            </button>

                            <form method="POST" action="<?php echo e(route('asset.subkategori.destroy', $sub->nid)); ?>"
                                class="d-inline" onsubmit="return confirm('Hapus sub kategori ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/master_sub_kategori.blade.php ENDPATH**/ ?>