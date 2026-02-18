
<div class="modal fade" id="modalSatuan">
    <div class="modal-dialog">
        <form method="POST" id="formSatuan">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="methodSatuan" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="titleSatuan">Tambah Satuan</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama Satuan</label>
                        <input type="text" name="nama" id="namaSatuan" class="form-control"
                            placeholder="Pcs / Unit / Set" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="btnSatuan">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    window.routeMsatuanStore = "<?php echo e(route('msatuan.store')); ?>";
</script>

<script src="<?php echo e(asset('js/asset.js')); ?>"></script>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/modal/modal_satuan.blade.php ENDPATH**/ ?>