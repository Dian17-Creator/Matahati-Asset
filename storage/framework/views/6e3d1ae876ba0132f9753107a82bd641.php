<!-- resources/views/penggajian/modals/modal_report_gaji.blade.php -->
<div class="modal fade" id="modalReportGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Export Report Gaji</h5>
            </div>

            <!-- target _blank agar file ter-download di tab baru -->
            <form id="formExportReportGaji" action="<?php echo e(route('payroll.export.report')); ?>" method="GET" target="_blank"
                autocomplete="off">
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="bulanInput" class="form-label">Pilih Bulan</label>
                        <!-- input type month -> value format yyyy-mm -->
                        <input id="bulanInput" name="bulan" type="month" class="form-control"
                            value="<?php echo e(($selYear ?? now()->year) . '-' . str_pad($selMonth ?? now()->month, 2, '0', STR_PAD_LEFT)); ?>">
                        <div class="form-text">Pilih periode laporan (tahun-bulan).</div>
                    </div>

                    <div class="mb-3">
                        <label for="reportDepartmentSelect" class="form-label">Filter Departemen</label>
                        <select id="reportDepartmentSelect" name="department_id" class="form-select">
                            <option value="">-- Semua Departemen --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <option value="<?php echo e($dep->nid ?? $dep->id); ?>"><?php echo e($dep->cname); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">Kosongkan untuk semua departemen.</div>
                    </div>

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
        const form = document.getElementById('formExportReportGaji');
        const bulanInput = document.getElementById('bulanInput');

        if (!form) return;

        form.addEventListener('submit', function(ev) {
            const val = (bulanInput && bulanInput.value) ? bulanInput.value.trim() : '';
            // basic validation: must match YYYY-MM
            if (!/^\d{4}-\d{2}$/.test(val)) {
                ev.preventDefault();
                alert('Silakan pilih bulan (format YYYY-MM).');
                bulanInput.focus();
                return false;
            }
            // allow submit (will open in new tab)
            return true;
        });
    });
</script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modals/modal_report_gaji.blade.php ENDPATH**/ ?>