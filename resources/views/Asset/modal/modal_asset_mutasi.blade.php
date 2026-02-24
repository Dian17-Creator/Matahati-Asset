<div class="modal fade" id="modalAssetMutasi" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.mutasi') }}">
            @csrf

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Mutasi Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- JENIS ASSET --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Asset</label>
                        <select name="jenis_asset" id="jenisAssetMutasi" class="form-select" required>
                            <option value="">-- Pilih Jenis Asset --</option>
                            <option value="QR">QR</option>
                            <option value="NON_QR">Non QR</option>
                        </select>
                    </div>

                    {{-- =========================
                        FORM QR
                    ========================= --}}
                    <div id="formMutasiQr" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Asset QR</label>
                            <select name="kode_asset_qr" id="assetQrSelect" class="form-select">
                                <option value="">-- Pilih Asset QR Aktif --</option>
                                @foreach ($assetQrAktif as $qr)
                                    <option value="{{ $qr->nid }}" data-niddept="{{ $qr->niddept }}">
                                        {{ $qr->cqr }}
                                        - {{ $qr->cnama ?? $qr->subKategori->cnama }}
                                        ({{ $qr->department->cname }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- LOKASI ASAL QR (HIDDEN, OPTIONAL) --}}
                        <input type="hidden" name="niddept_asal_qr" id="niddeptAsalQr">
                    </div>

                    {{-- =========================
                        FORM NON QR
                    ========================= --}}
                    <div id="formMutasiNonQr" class="d-none">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Asset Non QR</label>
                            <select name="kode_asset_nonqr" id="assetNonQrSelect" class="form-select">
                                <option value="">-- Pilih Asset Non QR --</option>
                                @foreach ($assetNonQrPemusnahan as $nonqr)
                                    <option value="{{ $nonqr->ckode }}" data-niddept="{{ $nonqr->niddept }}">
                                        {{ $nonqr->ckode }}
                                        - {{ $nonqr->cnama }}
                                        ({{ $nonqr->department->cname }} | Stok: {{ $nonqr->nqty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Qty Mutasi</label>
                            <input type="text" name="qty" class="form-control" min="1">
                        </div>

                        {{-- LOKASI ASAL (HIDDEN) --}}
                        <input type="hidden" name="niddept_asal" id="niddeptAsal">

                    </div>

                    {{-- LOKASI TUJUAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lokasi Tujuan</label>
                        <select name="niddept_tujuan" class="form-select" required>
                            <option value="">-- Pilih Lokasi Tujuan --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}">
                                    {{ $dept->cname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="ccatatan" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan Mutasi
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const jenis = document.getElementById('jenisAssetMutasi');
        const formQr = document.getElementById('formMutasiQr');
        const formNonQr = document.getElementById('formMutasiNonQr');

        const nonQrSelect = document.getElementById('assetNonQrSelect');
        const niddeptAsalInput = document.getElementById('niddeptAsal');

        // Toggle form QR / Non QR
        jenis.addEventListener('change', function() {

            formQr.classList.add('d-none');
            formNonQr.classList.add('d-none');

            if (this.value === 'QR') {
                formQr.classList.remove('d-none');
            }

            if (this.value === 'NON_QR') {
                formNonQr.classList.remove('d-none');
            }
        });

        // Ambil lokasi asal Non QR otomatis
        if (nonQrSelect) {
            nonQrSelect.addEventListener('change', function() {
                const selected = this.selectedOptions[0];
                niddeptAsalInput.value = selected?.dataset.niddept || '';
            });
        }

    });
</script>
