
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset Non QR</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetNonQr">
            + Tambah Asset Non QR
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Sub Kategori</th>
                    <th>Qty</th>
                    <th>Min Stok</th>
                    <th>Satuan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $assetNoQr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nqr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($nqr->department->cname); ?></td>
                        <td><?php echo e($nqr->subKategori->kategori->cnama); ?></td>
                        <td><?php echo e($nqr->subKategori->cnama); ?></td>
                        <td class="text-center"><?php echo e($nqr->nqty); ?></td>
                        <td class="text-center"><?php echo e($nqr->nminstok); ?></td>
                        <td><?php echo e($nqr->satuan?->nama ?? '-'); ?></td>
                        <td><?php echo e($nqr->ccatatan); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/master_asset_nonqr.blade.php ENDPATH**/ ?>