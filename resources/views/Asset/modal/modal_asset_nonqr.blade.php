<div class="modal fade" id="modalAssetQr">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.store') }}">
            @csrf

            <input type="hidden" name="jenis_asset" value="QR">

            <div class="modal-content">
                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5>Tambah Asset QR</h5>
                </div>

                <div class="modal-body">

                    {{-- LOKASI --}}
                    <div class="mb-2">
                        <label>Lokasi</label>
                        <select name="niddept" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}">{{ $dept->cname }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SUB KATEGORI QR --}}
                    <div class="mb-2">
                        <label>Sub Kategori (QR)</label>
                        <select name="nidsubkat" class="form-control" required>
                            @foreach ($subkategoriAll->where('fqr', 1) as $sub)
                                <option value="{{ $sub->nid }}">
                                    {{ $sub->kategori->cnama }} - {{ $sub->cnama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Nama Asset</label>
                        <input type="text" name="cnama" class="form-control" required>
                    </div>

                    {{-- TANGGAL BELI --}}
                    <div class="mb-2">
                        <label>Tanggal Beli</label>
                        <input type="date" name="dbeli" class="form-control">
                    </div>

                    {{-- HARGA BELI --}}
                    <div class="mb-2">
                        <label>Harga Beli</label>
                        <input type="text" name="nbeli" class="form-control" min="0">
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-2">
                        <label>Catatan</label>
                        <textarea name="ccatatan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
