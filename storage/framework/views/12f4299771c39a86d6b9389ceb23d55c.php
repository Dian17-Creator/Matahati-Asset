
<div class="modal fade" id="editUserModal<?php echo e($user->nid); ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('backoffice.updateUser', $user->nid)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row gx-2">
                        
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="email" class="form-control"
                                value="<?php echo e(old('email', $user->cemail)); ?>" required>
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Gmail</label>
                            <input type="email" name="cmailaddress" class="form-control"
                                value="<?php echo e(old('cmailaddress', $user->cmailaddress)); ?>" placeholder="">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin diubah">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="cphone" class="form-control"
                                value="<?php echo e(old('cphone', $user->cphone)); ?>" placeholder="">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>KTP</label>
                            <input type="text" name="cktp" class="form-control"
                                value="<?php echo e(old('cktp', $user->cktp)); ?>" placeholder="">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Finger ID (ID Mesin Finger)</label>
                            <input type="text" name="finger_id" class="form-control"
                                value="<?php echo e(old('finger_id', $user->finger_id)); ?>" placeholder="Contoh: 101">
                        </div>

                        
                        <div class="col-md-4 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control"
                                value="<?php echo e(old('name', $user->cname)); ?>" required>
                        </div>

                        
                        <div class="col-md-8 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="cfullname" class="form-control"
                                value="<?php echo e(old('cfullname', $user->cfullname)); ?>" placeholder="">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nomor Rekening</label>
                            <input type="text" name="caccnumber" class="form-control"
                                value="<?php echo e(old('caccnumber', $user->caccnumber)); ?>">
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="dtanggalmasuk" class="form-control"
                                value="<?php echo e(old('dtanggalmasuk', $user->dtanggalmasuk ? \Carbon\Carbon::parse($user->dtanggalmasuk)->format('Y-m-d') : '')); ?>">
                        </div>

                        
                        <?php
                            $allReks = isset($rekenings) ? $rekenings : collect();

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

                            // preferensi: old() -> muser.bank -> muser->rekening->bank
                            $currentBank = old('bank') ?? ($user->bank ?? ($user->rekening->bank ?? ''));
                            $currentRekeningId = old('rekening_id') ?? ($user->rekening_id ?? '');
                            $nid = $user->nid;
                        ?>

                        
                        <div class="col-12 mb-3">
                            <label>Jenis Bank</label>
                            <select name="bank" id="bankSelect<?php echo e($nid); ?>" class="form-control">
                                <option value="">-- Pilih Jenis Bank --</option>
                                <option value="Mandiri"
                                    <?php echo e(strcasecmp($currentBank, 'Mandiri') === 0 ? 'selected' : ''); ?>>Mandiri</option>
                                <option value="BCA" <?php echo e(strcasecmp($currentBank, 'BCA') === 0 ? 'selected' : ''); ?>>
                                    BCA</option>
                                <option value="BRI" <?php echo e(strcasecmp($currentBank, 'BRI') === 0 ? 'selected' : ''); ?>>
                                    BRI</option>
                                <option value="Lainnya"
                                    <?php echo e(strcasecmp($currentBank, 'Lainnya') === 0 ? 'selected' : ''); ?>>Lainnya</option>
                            </select>
                            <small class="text-muted">Jika memilih <strong>Mandiri</strong>, silakan pilih rekening
                                sumber di bawah.</small>
                        </div>

                        
                        <div class="col-12 mb-3" id="mandiriRekeningWrapper<?php echo e($nid); ?>" style="display: none;">
                            <label>Pilih Rekening Sumber (Mandiri)</label>
                            <select name="rekening_id" id="rekeningSelect<?php echo e($nid); ?>" class="form-control">
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

                    </div>

                    <div class="row gx-2">
                        
                        <div class="col-md-6 mb-3">
                            <label>Departemen</label>
                            <select name="niddept" class="form-control" required>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->nid); ?>"
                                        <?php echo e($user->niddept == $dept->nid ? 'selected' : ''); ?>>
                                        <?php echo e($dept->cname); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label>Status User</label>
                            <select name="factive" class="form-control" required>
                                <option value="1" <?php echo e($user->factive ? 'selected' : ''); ?>>
                                    Aktif
                                </option>
                                <option value="0" <?php echo e(!$user->factive ? 'selected' : ''); ?>>
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="mb-3">
                        <label>Role</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fadmin"
                                <?php echo e($user->fadmin ? 'checked' : ''); ?>>
                            <label class="form-check-label">Captain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsuper"
                                <?php echo e($user->fsuper ? 'checked' : ''); ?>>
                            <label class="form-check-label">Supervisor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsenior"
                                <?php echo e($user->fsenior ? 'checked' : ''); ?>>
                            <label class="form-check-label">Senior Crew</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="crew"
                                <?php echo e(!$user->fadmin && !$user->fsuper && !$user->fsenior ? 'checked' : ''); ?>>
                            <label class="form-check-label">Crew</label>
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        (function() {
            const nid = "<?php echo e($nid); ?>";
            const bankSelect = document.getElementById('bankSelect' + nid);
            const mandiriWrapper = document.getElementById('mandiriRekeningWrapper' + nid);
            const rekeningSelect = document.getElementById('rekeningSelect' + nid);
            const modalEl = document.getElementById('editUserModal' + nid);

            function isMandiri(value) {
                return String(value || '').toLowerCase() === 'mandiri';
            }

            function toggleMandiriDropdown() {
                if (!bankSelect) return;
                if (isMandiri(bankSelect.value)) {
                    mandiriWrapper.style.display = '';
                    rekeningSelect && rekeningSelect.setAttribute('required', 'required');
                } else {
                    mandiriWrapper.style.display = 'none';
                    rekeningSelect && rekeningSelect.removeAttribute('required');
                }
            }

            // inisialisasi saat DOM ready
            toggleMandiriDropdown();

            // re-check on change
            bankSelect && bankSelect.addEventListener('change', toggleMandiriDropdown);

            // juga re-check ketika modal dibuka (untuk nilai yang dipopulate oleh server/old())
            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', function() {
                    toggleMandiriDropdown();
                });
            }
        })();
    });
</script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/modal/modal_edit_user.blade.php ENDPATH**/ ?>