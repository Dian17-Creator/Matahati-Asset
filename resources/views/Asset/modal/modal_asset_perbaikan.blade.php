<div class="modal fade" id="modalAssetPerbaikan" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.qr.perbaikan') }}">
            @csrf

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Perbaikan Asset QR</h5>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- ASSET QR --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Asset QR</label>
                        <select name="kode_asset_qr" class="form-select" required>
                            <option value="">-- Pilih Asset QR --</option>
                            @foreach ($assetQrPerbaikan as $qr)
                                <<option value="{{ $qr->nid }}">
                                    {{ $qr->cqr }}
                                    - {{ $qr->cnama ?? $qr->subKategori->cnama }}
                                    [{{ $qr->cstatus }}]
                                    </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="cstatus" class="form-select" required>
                            <option value="Perbaikan">Perbaikan</option>
                            <option value="Aktif">Aktif</option>
                        </select>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="dtrans" class="form-control" required>
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
                    <button type="submit" class="btn btn-warning">
                        Simpan Perbaikan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
