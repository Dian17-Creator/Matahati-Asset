<div class="modal fade" id="modalAssetTrans">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.trans.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header" style="background-color:#B63352;color:white;">
                    <h5>Tambah Transaksi Asset</h5>
                </div>

                <div class="modal-body">

                    {{-- LOKASI --}}
                    <div class="mb-2">
                        <label>Lokasi</label>
                        <select name="nlokasi" id="lokasiSelect" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}">
                                    {{ $dept->cname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KODE ASSET (DROPDOWN) --}}
                    <div class="mb-2">
                        <label>Kode Asset</label>
                        <select name="ckode_asset" id="assetSelect" class="form-control" required>
                            <option value="">-- Pilih Kode Asset --</option>
                            @foreach ($assetDropdown as $a)
                                <option value="{{ $a['kode'] }}" data-lokasi="{{ $a['lokasi'] }}">
                                    [{{ $a['jenis'] }}] {{ $a['kode'] }} —
                                    {{ $a['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- MERK --}}
                    <div class="mb-2">
                        <label>Merk</label>
                        <input type="text" name="cmerk" class="form-control">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Beli</label>
                            <input type="date" name="dbeli" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Garansi Sampai</label>
                            <input type="date" name="dgaransi" class="form-control">
                        </div>
                    </div>

                    {{-- HARGA --}}
                    <div class="mb-2">
                        <label>Harga Beli</label>
                        <input type="number" name="nhrgbeli" class="form-control">
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-2">
                        <label>Catatan</label>
                        <textarea name="ccatatan" class="form-control"></textarea>
                    </div>

                    {{-- FOTO --}}
                    <div class="mb-2">
                        <label>Foto Asset (Opsional)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
