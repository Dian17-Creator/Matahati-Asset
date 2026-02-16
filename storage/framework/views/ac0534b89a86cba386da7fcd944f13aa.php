<div class="modal fade" id="modalHitungGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(url('penggajian/recalc-all')); ?>" id="formHitungGaji">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hitung Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="year" class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="year" id="year"
                            value="<?php echo e($year ?? date('Y')); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-control">
                            <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m); ?>"
                                    <?php echo e(isset($month) && $month == $m ? 'selected' : (date('n') == $m ? 'selected' : '')); ?>>
                                    <?php echo e(DateTime::createFromFormat('!m', $m)->format('F')); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- opsi split by change -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="split_by_change" name="split_by_change"
                            value="1" <?php echo e(old('split_by_change') ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="split_by_change">
                            Hitung sesuai perubahan dalam bulan (split by change)
                        </label>
                    </div>

                    <!-- NEW: opsi recalculate / force -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="recalculate" name="recalculate"
                            value="1">
                        <label class="form-check-label" for="recalculate">
                            Hitung Ulang
                        </label>
                        <small class="form-text text-muted">(Jika tidak dipilih, maka akan ditampilkan hasil perhitungan
                            sebelumnya)</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modal_hitung.blade.php ENDPATH**/ ?>