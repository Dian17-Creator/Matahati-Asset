<div class="modal fade" id="editDepartmentModal<?php echo e($dept->nid); ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('backoffice.updateDepartment', $dept->nid)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Departemen</label>
                        <input type="text" name="cname" class="form-control" value="<?php echo e($dept->cname); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/modal/modal_edit_department.blade.php ENDPATH**/ ?>