<!-- Source image (uploaded): /mnt/data/61e6e63f-88c3-4c3b-99b0-7a23ef6edb68.png -->

<!-- Modal Edit Payroll -->
<div class="modal fade" id="modalEditPayroll" tabindex="-1" aria-labelledby="modalEditPayrollLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditPayroll" method="post" action="">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?> 

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPayrollLabel">Edit Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" id="mp_user_id">

                    <div class="row g-3">

                        
                        <?php echo $__env->make('penggajian.modal_edit_fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    (function() {

        window.autoUpdate = false; // untuk menandai perubahan otomatis

        const UPDATE_ROUTE_TEMPLATE = "<?php echo e(route('gaji.update', ['id' => '__ID__'])); ?>";
        const form = document.getElementById('formEditPayroll');
        if (!form) return;

        const reasonInput = document.getElementById('mp_reasonedit');
        const errorText = document.getElementById('error-reasonedit');
        const saveBtn = form.querySelector('button[type="submit"]');

        /* =========================================
           1. BUKA MODAL + ISI DATA
        ========================================= */
        document.addEventListener('click', function(ev) {
            const btn = ev.target.closest('.btn-edit-payroll');
            if (!btn) return;

            ev.preventDefault();

            const id = btn.getAttribute('data-id');
            if (!id) {
                alert('Gagal membuka edit: data-id tidak ditemukan.');
                return;
            }

            const url = UPDATE_ROUTE_TEMPLATE.replace('__ID__', id);
            form.setAttribute('action', url);

            const setVal = (elId, value) => {
                const el = document.getElementById(elId);
                if (el) el.value = value ?? '';
            };

            const safe = (btn, key) => btn.getAttribute(key) ?? "";

            setVal('mp_tunjangan_makan', safe(btn, 'data-tunjangan_makan'));
            setVal('mp_tunjangan_jabatan', safe(btn, 'data-tunjangan_jabatan'));
            setVal('mp_tunjangan_transport', safe(btn, 'data-tunjangan_transport'));
            setVal('mp_tunjangan_luar_kota', safe(btn, 'data-tunjangan_luar_kota'));
            setVal('mp_tunjangan_masa_kerja', safe(btn, 'data-tunjangan_masa_kerja'));

            setVal('mp_gaji_lembur', safe(btn, 'data-gaji_lembur'));
            setVal('mp_tabungan_diambil', safe(btn, 'data-tabungan_diambil'));
            setVal('mp_potongan_lain', safe(btn, 'data-potongan_lain'));
            setVal('mp_potongan_tabungan', safe(btn, 'data-potongan_tabungan'));

            const noteField = document.getElementById('mp_note');
            if (noteField) noteField.value = btn.getAttribute('data-note') || '';

            // Reset validasi
            reasonInput.value = '';
            errorText.textContent = '';
            saveBtn.disabled = false;

            // Simpan nilai awal form
            initialData = {};
            form.querySelectorAll("input, textarea").forEach(el => {
                initialData[el.id] = el.value;
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditPayroll')).show();
        });


        /* =========================================
           2. DETEKSI PERUBAHAN MANUAL
        ========================================= */
        let initialData = {};

        function isChanged() {
            let changed = false;
            form.querySelectorAll("input, textarea").forEach(el => {
                if (initialData[el.id] !== el.value) changed = true;
            });
            return changed;
        }

        form.addEventListener('input', function() {

            // Jika perubahan otomatis, abaikan validasi
            if (window.autoUpdate) {
                saveBtn.disabled = false;
                errorText.textContent = "";
                return;
            }

            const changed = isChanged();
            const reason = reasonInput.value.trim();

            if (changed && reason === "") {
                errorText.textContent = "Alasan Edit wajib diisi jika Anda mengubah data.";
                saveBtn.disabled = true;
            } else {
                errorText.textContent = "";
                saveBtn.disabled = false;
            }
        });

    })();
</script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modal_edit.blade.php ENDPATH**/ ?>