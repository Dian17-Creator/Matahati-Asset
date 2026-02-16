<div class="modal fade" id="modalTambahRekening" tabindex="-1" aria-labelledby="modalTambahRekeningLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTambahRekeningLabel">Tambah Rekening</h5>
            </div>

            <form action="<?php echo e(route('mrekening.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="modal-body">

                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_rekening" class="form-control" required
                            placeholder="Masukkan nomor rekening">
                    </div>

                    
                    <div class="mb-3">
                        <label class="form-label">Bank <span class="text-danger">*</span></label>
                        <select name="bank" class="form-select" required>
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                        </select>
                    </div>

                    
                    <div class="mb-3">
                        <label class="form-label">Atas Nama <span class="text-danger">*</span></label>
                        <input type="text" name="atas_nama" class="form-control" required
                            placeholder="Nama pemilik rekening">
                    </div>

                    
                    <div class="mb-3">
                        <label class="form-label">Cabang</label>
                        <input type="text" name="cabang" class="form-control"
                            placeholder="Contoh: Tulungagung, Kediri, Blitar">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/penggajian/modals/modal_tambah_rekening.blade.php ENDPATH**/ ?>