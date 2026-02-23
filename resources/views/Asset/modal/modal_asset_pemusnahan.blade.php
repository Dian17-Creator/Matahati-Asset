<div class="modal fade" id="modalAssetPemusnahan" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.pemusnahan') }}">
            @csrf

            <div class="modal-content">
                {{-- HEADER --}}
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Pemusnahan Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- JENIS ASSET --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Asset</label>
                        <select name="jenis_asset" id="jenisAsset" class="form-select" required>
                            <option value="">-- Pilih Jenis Asset --</option>
                            <option value="QR">QR</option>
                            <option value="NON_QR">Non QR</option>
                        </select>
                    </div>

                    {{-- FORM QR --}}
                    <div id="formQr" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Kode Asset (QR)</label>
                            <select name="kode_asset_qr" id="kodeAssetQr" class="form-select">
                                <option value="">-- Pilih Asset QR Aktif --</option>
                                @foreach ($assetQrAktif as $qr)
                                    <option value="{{ $qr->nid }}">
                                        {{ $qr->cqr }} - {{ $qr->subKategori->cnama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- FORM NON QR --}}
                    <div id="formNonQr" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Kode Asset (Non QR)</label>
                            <select name="kode_asset_nonqr" class="form-select" required>
                                <option value="">-- Pilih Asset Non QR --</option>
                                @foreach ($assetNonQrPemusnahan as $nonqr)
                                    <option value="{{ $nonqr->ckode }}">
                                        {{ $nonqr->ckode }} - {{ $nonqr->cnama }} (Stok: {{ $nonqr->nqty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qty Pemusnahan</label>
                            <input type="text" name="qty" class="form-control" min="1">
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="ccatatan" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Simpan Pemusnahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenis = document.getElementById('jenisAsset');
        const formQr = document.getElementById('formQr');
        const formNonQr = document.getElementById('formNonQr');

        const qrSelect = formQr.querySelector('select');
        const nonQrSelect = formNonQr.querySelector('select');

        jenis.addEventListener('change', function() {
            formQr.classList.add('d-none');
            formNonQr.classList.add('d-none');

            qrSelect.disabled = true;
            nonQrSelect.disabled = true;

            if (this.value === 'QR') {
                formQr.classList.remove('d-none');
                qrSelect.disabled = false;
            }

            if (this.value === 'NON_QR') {
                formNonQr.classList.remove('d-none');
                nonQrSelect.disabled = false;
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector('#modalAssetPemusnahan form');

        if (!form) return;

        form.addEventListener('submit', function() {

            const data = {
                jenis_asset: document.getElementById('jenisAsset')?.value || null,
                kode_asset_qr: document.querySelector('[name="kode_asset_qr"]')?.value || null,
                kode_asset_nonqr: document.querySelector('[name="kode_asset_nonqr"]')?.value ||
                    null,
                qty: document.querySelector('[name="qty"]')?.value || null,
                ccatatan: document.querySelector('[name="ccatatan"]')?.value || null,
                time: new Date().toISOString()
            };

            // simpan ke sessionStorage
            sessionStorage.setItem('debug_pemusnahan', JSON.stringify(data));
        });
    });
</script>
