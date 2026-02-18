
<div class="modal fade" id="modalAsset">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('asset.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Asset</h5>
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
                        <label>Kategori</label>
                        <select id="filterKategori" class="form-control">
                            <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kat->nid); ?>"><?php echo e($kat->cnama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="mb-2">
                        <label>Sub Kategori</label>
                        <select name="nidsubkat" id="filterSubKategori" class="form-control" required>
                            <?php $__currentLoopData = $subkategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sub->nid); ?>" data-kat="<?php echo e($sub->nidkat); ?>"
                                    data-fqr="<?php echo e($sub->fqr); ?>">
                                    <?php echo e($sub->cnama); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="mb-2">
                        <label>Jenis Asset</label>
                        <input type="text" id="jenisAsset" class="form-control" readonly>
                    </div>

                    
                    <div id="fieldQr" style="display:none">
                        <div class="mb-2">
                            <label>Tanggal Beli</label>
                            <input type="date" name="dbeli" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Harga Beli</label>
                            <input type="number" name="nbeli" class="form-control" min="0">
                        </div>
                    </div>

                    
                    <div id="fieldNonQr" style="display:none">
                        <div class="mb-2">
                            <label>Qty</label>
                            <input type="number" name="nqty" class="form-control" min="1">
                        </div>

                        <div class="mb-2">
                            <label>Min Stok</label>
                            <input type="number" name="nminstok" class="form-control" min="0">
                        </div>

                        <div class="mb-2">
                            <label>Satuan</label>
                            <select name="msatuan_id" class="form-control">
                                <option value="">-- Pilih Satuan --</option>
                                <?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s->id); ?>"><?php echo e($s->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    
                    <div class="mb-2">
                        <label>Catatan</label>
                        <textarea name="ccatatan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-warning">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo e(asset('js/asset.js')); ?>"></script>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/modal/modal_asset.blade.php ENDPATH**/ ?>