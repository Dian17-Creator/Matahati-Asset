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
                        <select name="nlokasi" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}">{{ $dept->cname }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KATEGORI & SUB KATEGORI --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Kategori</label>
                            <select id="katSelect" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriAll as $kat)
                                    <option value="{{ $kat->nid }}">{{ $kat->cnama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Sub Kategori</label>
                            <select name="ngrpid" id="subkatSelect" class="form-control" required>
                                <option value="">-- Pilih Sub Kategori --</option>
                                @foreach ($subkategori as $sub)
                                    <option value="{{ $sub->nid }}" data-kat="{{ $sub->nidkat }}"
                                        data-fqr="{{ $sub->fqr }}">
                                        {{ $sub->kategori->cnama }} - {{ $sub->cnama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- KODE ASSET --}}
                    <div class="mb-2">
                        <label>Kode Asset</label>
                        <input type="text" name="ckode" id="kodeAsset" class="form-control" required>
                        <small class="text-muted" id="kodeHint"></small>
                    </div>

                    <div class="row">
                        {{-- NAMA --}}
                        <div class="col-md-6 mb-3">
                            <label>Nama Asset</label>
                            <input type="text" name="cnama" class="form-control">
                        </div>

                        {{-- MERK --}}
                        <div class="col-md-6 mb-3">
                            <label>Merk</label>
                            <input type="text" name="cmerk" class="form-control">
                        </div>

                    </div>


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
