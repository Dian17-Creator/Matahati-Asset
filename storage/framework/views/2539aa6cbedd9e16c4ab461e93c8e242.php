
<div class="modal fade" id="modalSubKategori">
    <div class="modal-dialog">
        <form method="POST" id="formSubKategori" action="<?php echo e(route('asset.subkategori.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="methodSubKategori" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="titleSubKategori">Tambah Sub Kategori</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Kategori</label>
                        <select name="nidkat" class="form-control" required>
                            <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kat->nid); ?>"><?php echo e($kat->cnama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Kode</label>
                        <input type="text" name="ckode" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="cnama" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Jenis Asset</label>
                        <select name="fqr" class="form-control">
                            <option value="1">QR</option>
                            <option value="0">Non QR</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo e(asset('js/asset.js')); ?>"></script>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/modal/modal_sub_kategori.blade.php ENDPATH**/ ?>