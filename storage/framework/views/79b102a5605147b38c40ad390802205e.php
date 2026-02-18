<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset QR</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetQr">
            + Tambah Asset QR
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Sub Kategori</th>
                    <th>Counter</th>
                    <th>QR Code</th>
                    <th>Tgl Beli</th>
                    <th>Harga Beli</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $assetQr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($qr->department->cname); ?></td>
                        <td><?php echo e($qr->subKategori->kategori->cnama); ?></td>
                        <td><?php echo e($qr->subKategori->cnama); ?></td>
                        <td class="text-center"><?php echo e($qr->nurut); ?></td>
                        <td class="text-center"><?php echo e($qr->cqr); ?></td>

                        
                        <td class="text-center">
                            <?php echo e($qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-'); ?>

                        </td>

                        
                        <td class="text-center">
                            <?php echo e($qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-'); ?>

                        </td>

                        <td><?php echo e($qr->cstatus); ?></td>
                        <td><?php echo e($qr->ccatatan); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>

        </table>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/master_asset_qr.blade.php ENDPATH**/ ?>