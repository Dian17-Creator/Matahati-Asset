<!-- resources/views/penggajian/modals/modal_payroll_bank.blade.php -->
<div class="modal fade" id="modalPayrollBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Export Payroll Bank</h5>
            </div>

            
            <form id="formExportBank" action="<?php echo e(route('payroll.export.bank')); ?>" method="GET" autocomplete="off">
                <input type="hidden" name="bulan"
                    value="<?php echo e($selYear); ?>-<?php echo e(str_pad($selMonth, 2, '0', STR_PAD_LEFT)); ?>">

                <div class="modal-body">

                    <!-- Tanggal Payroll -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Payroll</label>
                        <input type="date" name="payroll_date" class="form-control">
                    </div>

                    <!-- FILTER DEPARTEMEN — VALUE = NID (sesuai muser.niddept) -->
                    <div class="mb-3">
                        <label class="form-label">Filter Departemen</label>
                        <select name="department_id" id="departmentSelect" class="form-select">
                            <option value="">-- Semua Departemen --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <option value="<?php echo e($dep->nid); ?>"><?php echo e($dep->cname); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- PILIH FORMAT BANK -->
                    <div class="mb-3">
                        <label class="form-label">Format Bank</label>
                        <select name="bank" id="bankSelect" class="form-select" required>
                            <option value="">-- Pilih Format --</option>
                            <option value="bca">BCA</option>
                            <option value="mandiri">Mandiri</option>
                            <option value="bri">BRI</option>
                        </select>
                    </div>

                    <!-- REKENING MANDIRI (hanya tampil saat Mandiri dipilih) -->
                    <div class="mb-3" id="rekeningMandiriBox" style="display:none;">
                        <label class="form-label">Pilih Rekening Sumber (Mandiri)</label>
                        <select name="mrekening_id" id="mrekeningSelect" class="form-select">
                            <option value="">-- Pilih Rekening --</option>
                            <?php $__currentLoopData = $mrekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <option value="<?php echo e($rek->id); ?>" data-bank="<?php echo e(strtolower($rek->bank ?? '')); ?>">
                                    <?php echo e($rek->bank); ?> - <?php echo e($rek->atas_nama); ?> - <?php echo e($rek->nomor_rekening); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- OPTIONAL: hidden selected_ids (tidak wajib dipakai; controller menangani) -->
                    <input type="hidden" name="selected_ids" id="selected_ids" value="">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bankSelect = document.getElementById('bankSelect');
        const rekeningBox = document.getElementById('rekeningMandiriBox');
        const mrekeningSelect = document.getElementById('mrekeningSelect');
        const form = document.getElementById('formExportBank');

        function toggleMandiriOptions() {
            if (!bankSelect) return;
            const isMandiri = String(bankSelect.value || '').toLowerCase() === 'mandiri';
            if (rekeningBox) rekeningBox.style.display = isMandiri ? 'block' : 'none';
            if (mrekeningSelect) {
                Array.from(mrekeningSelect.options).forEach(o => {
                    const b = (o.dataset.bank || '').toLowerCase();
                    o.style.display = (!isMandiri || b.includes('mandiri')) ? '' : 'none';
                });
                if (!isMandiri) mrekeningSelect.value = '';
            }
        }

        // init toggle on load
        toggleMandiriOptions();
        if (bankSelect) bankSelect.addEventListener('change', toggleMandiriOptions);

        // optional: basic client-side guard to avoid empty bank / mandiri without rekening
        if (form) {
            form.addEventListener('submit', function(ev) {
                const bank = (bankSelect && bankSelect.value) ? bankSelect.value.trim() : '';
                if (!bank) {
                    alert('Pilih format bank terlebih dahulu.');
                    ev.preventDefault();
                    return;
                }
                if (bank.toLowerCase() === 'mandiri' && mrekeningSelect && !mrekeningSelect.value) {
                    alert('Silakan pilih Rekening Sumber untuk Mandiri.');
                    ev.preventDefault();
                    return;
                }
                // allow submit — controller akan menangani department filtering
            });
        }
    });
</script>
<?php /**PATH D:\Matahati-Asset\resources\views/penggajian/modals/modal_payroll_bank.blade.php ENDPATH**/ ?>