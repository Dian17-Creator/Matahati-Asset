<div class="modal fade" id="modalPayrollBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Export Payroll Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?php echo e(route('payroll.export.bank')); ?>" method="GET" target="_blank">

                <input type="hidden" name="bulan"
                    value="<?php echo e($selYear); ?>-<?php echo e(str_pad($selMonth, 2, '0', STR_PAD_LEFT)); ?>">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format Bank</label>
                        <select name="bank" class="form-select" required>
                            <option value="">-- Pilih Format --</option>
                            <option value="bca">BCA</option>
                            <option value="mandiri">Mandiri</option>
                            
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modal_payroll_bank.blade.php ENDPATH**/ ?>