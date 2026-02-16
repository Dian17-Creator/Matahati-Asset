
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('backoffice.add')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row gx-2">
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="email" class="form-control" value="<?php echo e(old('email')); ?>"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Gmail</label>
                            <input type="email" name="cmailaddress" class="form-control"
                                value="<?php echo e(old('cmailaddress')); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="cphone" class="form-control" value="<?php echo e(old('cphone')); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>KTP</label>
                            <input type="text" name="cktp" class="form-control" value="<?php echo e(old('cktp')); ?>">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Finger ID (ID Mesin Finger)</label>
                            <input type="text" name="finger_id" class="form-control" value="<?php echo e(old('finger_id')); ?>"
                                placeholder="Contoh: 101">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>"
                                required>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="cfullname" class="form-control" value="<?php echo e(old('cfullname')); ?>">
                        </div>

                        <div class="mb-3">
                            <label>Nomor Rekening</label>
                            <input type="text" name="caccnumber" class="form-control" value="<?php echo e(old('caccnumber')); ?>"
                                placeholder="">
                        </div>

                        
                        <?php
                            $allReks = isset($rekenings) ? $rekenings : collect();

                            // unik berdasarkan bank + nomor_rekening
                            $uniqueReks = $allReks
                                ->unique(function ($r) {
                                    $bank = isset($r->bank) ? strtolower(trim($r->bank)) : '';
                                    $nom = isset($r->nomor_rekening)
                                        ? preg_replace('/\D+/', '', (string) $r->nomor_rekening)
                                        : '';
                                    return $bank . '|' . $nom;
                                })
                                ->values();

                            $mandiriReks = $uniqueReks
                                ->filter(function ($r) {
                                    return isset($r->bank) && strtolower(trim($r->bank)) === 'mandiri';
                                })
                                ->values();

                            $currentBank = old('bank', '');
                            $currentRekeningId = old('rekening_id', '');
                        ?>

                        
                        <div class="mb-3">
                            <label>Jenis Bank</label>
                            <select name="bank" id="bankSelect" class="form-control">
                                <option value="">-- Pilih Jenis Bank --</option>
                                <option value="Mandiri"
                                    <?php echo e(strcasecmp($currentBank, 'Mandiri') === 0 ? 'selected' : ''); ?>>Mandiri</option>
                                <option value="BCA" <?php echo e(strcasecmp($currentBank, 'BCA') === 0 ? 'selected' : ''); ?>>
                                    BCA</option>
                                <option value="BRI" <?php echo e(strcasecmp($currentBank, 'BRI') === 0 ? 'selected' : ''); ?>>
                                    BRI</option>
                            </select>
                            <small class="text-muted">Jika memilih <strong>Mandiri</strong>, silakan pilih rekening
                                sumber perusahaan di bawah.</small>
                        </div>

                        
                        <div class="mb-3" id="mandiriRekeningWrapper" style="display: none;">
                            <label>Pilih Rekening Sumber (Mandiri)</label>
                            <select name="rekening_id" id="rekeningSelect" class="form-control">
                                <option value="">-- Pilih Rekening --</option>

                                <?php if($mandiriReks->count()): ?>
                                    <?php $__currentLoopData = $mandiriReks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $nom = $rek->nomor_rekening ?? '';
                                            $nomDisp = $nom ? preg_replace('/\D+/', '', (string) $nom) : '';
                                            $bankLabel = strtoupper($rek->bank ?? '');
                                            $atasNama = $rek->atas_nama ?? '';
                                            $label = trim(
                                                $bankLabel .
                                                    ($nomDisp ? " - {$nomDisp}" : '') .
                                                    ($atasNama ? " ({$atasNama})" : ''),
                                            );
                                        ?>
                                        <option value="<?php echo e($rek->id); ?>"
                                            <?php echo e((string) $rek->id === (string) $currentRekeningId ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option disabled>Belum ada data rekening Mandiri</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="dtanggalmasuk" class="form-control"
                                value="<?php echo e(old('dtanggalmasuk')); ?>">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label>Departemen</label>
                        <select name="niddept" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dept->nid); ?>"
                                    <?php echo e(old('niddept') == $dept->nid ? 'selected' : ''); ?>>
                                    <?php echo e($dept->cname); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Role User</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fadmin"
                                <?php echo e(old('role') === 'fadmin' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Captain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsuper"
                                <?php echo e(old('role') === 'fsuper' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Supervisor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsenior"
                                <?php echo e(old('role') === 'fsenior' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Senior Crew</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="crew"
                                <?php echo e(old('role', 'crew') === 'crew' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Crew</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bankSelect = document.getElementById('bankSelect');
        const mandiriWrapper = document.getElementById('mandiriRekeningWrapper');
        const rekeningSelect = document.getElementById('rekeningSelect');
        const addModal = document.getElementById('addUserModal');

        function isMandiri(value) {
            return String(value || '').toLowerCase() === 'mandiri';
        }

        function toggleMandiriDropdown() {
            if (!bankSelect) return;
            if (isMandiri(bankSelect.value)) {
                mandiriWrapper.style.display = '';
                if (rekeningSelect) rekeningSelect.setAttribute('required', 'required');
            } else {
                mandiriWrapper.style.display = 'none';
                if (rekeningSelect) {
                    rekeningSelect.removeAttribute('required');
                    // kosongkan pilihan supaya tidak tersubmit nilai lama
                    rekeningSelect.value = '';
                }
            }
        }

        // inisialisasi saat DOM ready dan saat modal dibuka
        toggleMandiriDropdown();
        if (bankSelect) bankSelect.addEventListener('change', toggleMandiriDropdown);

        if (addModal) {
            addModal.addEventListener('show.bs.modal', function() {
                toggleMandiriDropdown();
            });
        }
    });
</script>
<?php /**PATH D:\Matahati-Asset\resources\views/backoffice/modal/modal_tambah_user.blade.php ENDPATH**/ ?>