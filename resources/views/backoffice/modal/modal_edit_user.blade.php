{{-- resources/views/backoffice/partials/modal_edit_user.blade.php --}}
<div class="modal fade" id="editUserModal{{ $user->nid }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('backoffice.updateUser', $user->nid) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row gx-2">
                        {{-- Username --}}
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="email" class="form-control"
                                value="{{ old('email', $user->cemail) }}" required>
                        </div>

                        {{-- Gmail --}}
                        <div class="col-md-6 mb-3">
                            <label>Gmail</label>
                            <input type="email" name="cmailaddress" class="form-control"
                                value="{{ old('cmailaddress', $user->cmailaddress) }}" placeholder="">
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin diubah">
                        </div>

                        {{-- No Telepon --}}
                        <div class="col-md-6 mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="cphone" class="form-control"
                                value="{{ old('cphone', $user->cphone) }}" placeholder="">
                        </div>

                        {{-- KTP --}}
                        <div class="col-md-6 mb-3">
                            <label>KTP</label>
                            <input type="text" name="cktp" class="form-control"
                                value="{{ old('cktp', $user->cktp) }}" placeholder="">
                        </div>

                        {{-- Finger ID --}}
                        <div class="col-md-6 mb-3">
                            <label>Finger ID (ID Mesin Finger)</label>
                            <input type="text" name="finger_id" class="form-control"
                                value="{{ old('finger_id', $user->finger_id) }}" placeholder="Contoh: 101">
                        </div>

                        {{-- Nama --}}
                        <div class="col-md-4 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $user->cname) }}" required>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="col-md-8 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="cfullname" class="form-control"
                                value="{{ old('cfullname', $user->cfullname) }}" placeholder="">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nomor Rekening</label>
                            <input type="text" name="caccnumber" class="form-control"
                                value="{{ old('caccnumber', $user->caccnumber) }}">
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="dtanggalmasuk" class="form-control"
                                value="{{ old('dtanggalmasuk', $user->dtanggalmasuk ? \Carbon\Carbon::parse($user->dtanggalmasuk)->format('Y-m-d') : '') }}">
                        </div>

                        {{-- Prepare rekening collections (dedup & filter) --}}
                        @php
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
                        @endphp

                        {{-- Jenis Bank --}}
                        <div class="col-12 mb-3">
                            <label>Jenis Bank</label>
                            <select name="bank" id="bankSelect{{ $nid }}" class="form-control">
                                <option value="">-- Pilih Jenis Bank --</option>
                                <option value="Mandiri"
                                    {{ strcasecmp($currentBank, 'Mandiri') === 0 ? 'selected' : '' }}>Mandiri</option>
                                <option value="BCA" {{ strcasecmp($currentBank, 'BCA') === 0 ? 'selected' : '' }}>
                                    BCA</option>
                                <option value="BRI" {{ strcasecmp($currentBank, 'BRI') === 0 ? 'selected' : '' }}>
                                    BRI</option>
                                <option value="Lainnya"
                                    {{ strcasecmp($currentBank, 'Lainnya') === 0 ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <small class="text-muted">Jika memilih <strong>Mandiri</strong>, silakan pilih rekening
                                sumber di bawah.</small>
                        </div>

                        {{-- Rekening Sumber (hanya Mandiri) --}}
                        <div class="col-12 mb-3" id="mandiriRekeningWrapper{{ $nid }}" style="display: none;">
                            <label>Pilih Rekening Sumber (Mandiri)</label>
                            <select name="rekening_id" id="rekeningSelect{{ $nid }}" class="form-control">
                                <option value="">-- Pilih Rekening --</option>

                                @if ($mandiriReks->count())
                                    @foreach ($mandiriReks as $rek)
                                        @php
                                            $nom = $rek->nomor_rekening ?? '';
                                            $nomDisp = $nom ? preg_replace('/\D+/', '', (string) $nom) : '';
                                            $bankLabel = strtoupper($rek->bank ?? '');
                                            $atasNama = $rek->atas_nama ?? '';
                                            $label = trim(
                                                $bankLabel .
                                                    ($nomDisp ? " - {$nomDisp}" : '') .
                                                    ($atasNama ? " ({$atasNama})" : ''),
                                            );
                                        @endphp
                                        <option value="{{ $rek->id }}"
                                            {{ (string) $rek->id === (string) $currentRekeningId ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>Belum ada data rekening Mandiri</option>
                                @endif
                            </select>
                        </div>

                    </div>

                    <div class="row gx-2">
                        {{-- Departemen --}}
                        <div class="col-md-6 mb-3">
                            <label>Departemen</label>
                            <select name="niddept" class="form-control" required>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->nid }}"
                                        {{ $user->niddept == $dept->nid ? 'selected' : '' }}>
                                        {{ $dept->cname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status User --}}
                        <div class="col-md-6 mb-3">
                            <label>Status User</label>
                            <select name="factive" class="form-control" required>
                                <option value="1" {{ $user->factive ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="0" {{ !$user->factive ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label>Role</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fadmin"
                                {{ $user->fadmin ? 'checked' : '' }}>
                            <label class="form-check-label">Captain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsuper"
                                {{ $user->fsuper ? 'checked' : '' }}>
                            <label class="form-check-label">Supervisor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsenior"
                                {{ $user->fsenior ? 'checked' : '' }}>
                            <label class="form-check-label">Senior Crew</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="crew"
                                {{ !$user->fadmin && !$user->fsuper && !$user->fsenior ? 'checked' : '' }}>
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
            const nid = "{{ $nid }}";
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
