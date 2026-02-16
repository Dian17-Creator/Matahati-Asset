
<div class="modal fade" id="exportUserModal" tabindex="-1" aria-labelledby="exportUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="exportUserModalLabel">
                    Export Daftar User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-3">
                    Pilih format export yang diinginkan
                </p>

                <div class="d-grid gap-3">
                    
                    <a href="<?php echo e(route('backoffice.users.export.excel')); ?>" class="btn btn-success">
                        📊 Export ke Excel
                        <div class="small text-white-50">
                            Data tabel (tanpa foto / link foto)
                        </div>
                    </a>

                    
                    <a href="<?php echo e(route('backoffice.users.export.pdf')); ?>" class="btn btn-danger">
                        📄 Export ke PDF
                        <div class="small text-white-50">
                            Lengkap dengan foto wajah
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
<?php /**PATH /home/matahati/domains/asset.matahati.my.id/public_html/laravel/resources/views/backoffice/modal/modal_export_user.blade.php ENDPATH**/ ?>