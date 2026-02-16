<div class="modal fade" id="modalEditRekening" tabindex="-1" aria-labelledby="modalEditRekeningLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEditRekeningLabel">Edit Rekening</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- NOTE: action kosong, akan di-set oleh JS -->
            <form id="formEditRekening" method="POST" action="">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="nomor_rekening" id="edit_nomor_rekening"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bank</label>
                        <select class="form-select" name="bank" id="edit_bank" required>
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" name="atas_nama" id="edit_atas_nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cabang</label>
                        <input type="text" class="form-control" name="cabang" id="edit_cabang">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-edit-rekening').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nomor = this.dataset.nomor ?? '';
                const bank = this.dataset.bank ?? '';
                const atas = this.dataset.atas ?? '';
                const cabang = this.dataset.cabang ?? '';

                // Set form action menggunakan route() yang di-generate Blade (aman untuk prefix)
                // Gunakan pattern route yang digenerate server lalu ganti placeholder :id
                const baseAction = "<?php echo e(route('mrekening.update', ':id')); ?>"; // blade
                const action = baseAction.replace(':id', id);

                const form = document.getElementById('formEditRekening');
                form.action = action;

                // Set fields
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nomor_rekening').value = nomor;
                document.getElementById('edit_bank').value = bank;
                document.getElementById('edit_atas_nama').value = atas;
                document.getElementById('edit_cabang').value = cabang;
            });
        });
    });
</script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modals/modal_edit_rekening.blade.php ENDPATH**/ ?>