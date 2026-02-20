<div class="modal fade" id="modalAssetNonQr">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.store') }}">
            @csrf

            <input type="hidden" name="jenis_asset" value="NONQR">

            <div class="modal-content">
                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5>Tambah Asset Non-QR</h5>
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

                    {{-- SUB KATEGORI NON QR --}}
                    <div class="mb-2">
                        <label>Sub Kategori (Non-QR)</label>
                        <select name="nidsubkat" class="form-control" required>
                            @foreach ($subkategori->where('fqr', 0) as $sub)
                                <option value="{{ $sub->nid }}">
                                    {{ $sub->kategori->cnama }} - {{ $sub->cnama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- QTY --}}
                    <div class="mb-2">
                        <label>Qty</label>
                        <input type="number" name="nqty" class="form-control" min="1" required>
                    </div>

                    {{-- MIN STOK --}}
                    <div class="mb-2">
                        <label>Min Stok</label>
                        <input type="number" name="nminstok" class="form-control" min="0">
                    </div>

                    {{-- SATUAN --}}
                    <div class="mb-2">
                        <label>Satuan</label>
                        <select name="msatuan_id" class="form-control">
                            @foreach ($SatuanAll as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
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
