{{-- resources/views/backoffice/partials/modal_tambah_user.blade.php --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('backoffice.add') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row gx-2">
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="email" class="form-control" value="{{ old('email') }}"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Gmail</label>
                            <input type="email" name="cmailaddress" class="form-control"
                                value="{{ old('cmailaddress') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="cphone" class="form-control" value="{{ old('cphone') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>KTP</label>
                            <input type="text" name="cktp" class="form-control" value="{{ old('cktp') }}">
                        </div>

                        {{-- Finger ID --}}
                        <div class="col-md-6 mb-3">
                            <label>Finger ID (ID Mesin Finger)</label>
                            <input type="text" name="finger_id" class="form-control" value="{{ old('finger_id') }}"
                                placeholder="Contoh: 101">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                required>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="cfullname" class="form-control" value="{{ old('cfullname') }}">
                        </div>

                        <div class="mb-3">
                            <label>Nomor Rekening</label>
                            <input type="text" name="caccnumber" class="form-control" value="{{ old('caccnumber') }}"
                                placeholder="">
                        </div>

                        {{-- Prepare rekenings --}}
                        @php
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
                        @endphp

                        {{-- Jenis bank (enum: BCA, BRI, Mandiri) --}}
                        <div class="mb-3">
                            <label>Jenis Bank</label>
                            <select name="bank" id="bankSelect" class="form-control">
                                <option value="">-- Pilih Jenis Bank --</option>
                                <option value="Mandiri"
                                    {{ strcasecmp($currentBank, 'Mandiri') === 0 ? 'selected' : '' }}>Mandiri</option>
                                <option value="BCA" {{ strcasecmp($currentBank, 'BCA') === 0 ? 'selected' : '' }}>
                                    BCA</option>
                                <option value="BRI" {{ strcasecmp($currentBank, 'BRI') === 0 ? 'selected' : '' }}>
                                    BRI</option>
                            </select>
                            <small class="text-muted">Jika memilih <strong>Mandiri</strong>, silakan pilih rekening
                                sumber perusahaan di bawah.</small>
                        </div>

                        {{-- Jika Mandiri -> tampilkan dropdown rekening sumber --}}
                        <div class="mb-3" id="mandiriRekeningWrapper" style="display: none;">
                            <label>Pilih Rekening Sumber (Mandiri)</label>
                            <select name="rekening_id" id="rekeningSelect" class="form-control">
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

                        <div class="mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="dtanggalmasuk" class="form-control"
                                value="{{ old('dtanggalmasuk') }}">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label>Departemen</label>
                        <select name="niddept" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}"
                                    {{ old('niddept') == $dept->nid ? 'selected' : '' }}>
                                    {{ $dept->cname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Role User</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fadmin"
                                {{ old('role') === 'fadmin' ? 'checked' : '' }}>
                            <label class="form-check-label">Captain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsuper"
                                {{ old('role') === 'fsuper' ? 'checked' : '' }}>
                            <label class="form-check-label">Supervisor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="fsenior"
                                {{ old('role') === 'fsenior' ? 'checked' : '' }}>
                            <label class="form-check-label">Senior Crew</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" value="crew"
                                {{ old('role', 'crew') === 'crew' ? 'checked' : '' }}>
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

{{-- JS: toggle tampilnya dropdown rekening sumber ketika bank = Mandiri --}}
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
