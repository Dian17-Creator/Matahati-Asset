<div class="modal fade" id="modalAssetQr">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('asset.store')); ?>">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="jenis_asset" value="QR">

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Asset QR</h5>
                </div>

                <div class="modal-body">

                    
                    <div class="mb-2">
                        <label>Lokasi</label>
                        <select name="niddept" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dept->nid); ?>"><?php echo e($dept->cname); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="mb-2">
                        <label>Sub Kategori (QR)</label>
                        <select name="nidsubkat" class="form-control" required>
                            <?php $__currentLoopData = $subkategori->where('fqr', 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sub->nid); ?>">
                                    <?php echo e($sub->kategori->cnama); ?> - <?php echo e($sub->cnama); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="mb-2">
                        <label>Tanggal Beli</label>
                        <input type="date" name="dbeli" class="form-control">
                    </div>

                    
                    <div class="mb-2">
                        <label>Harga Beli</label>
                        <input type="number" name="nbeli" class="form-control" min="0">
                    </div>

                    
                    <div class="mb-2">
                        <label>Catatan</label>
                        <textarea name="ccatatan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-warning">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/modal/modal_asset_nonqr.blade.php ENDPATH**/ ?>