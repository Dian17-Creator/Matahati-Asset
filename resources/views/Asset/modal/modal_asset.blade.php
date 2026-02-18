{{-- ================= MODAL ASSET ================= --}}
<div class="modal fade" id="modalAsset">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Asset</h5>
                </div>

                <div class="modal-body">

                    {{-- DEPARTMENT --}}
                    <div class="mb-2">
                        <label>Lokasi</label>
                        <select name="niddept" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->nid }}">{{ $dept->cname }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="mb-2">
                        <label>Kategori</label>
                        <select id="filterKategori" class="form-control">
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->nid }}">{{ $kat->cnama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SUB KATEGORI --}}
                    <div class="mb-2">
                        <label>Sub Kategori</label>
                        <select name="nidsubkat" id="filterSubKategori" class="form-control" required>
                            @foreach ($subkategori as $sub)
                                <option value="{{ $sub->nid }}" data-kat="{{ $sub->nidkat }}"
                                    data-fqr="{{ $sub->fqr }}">
                                    {{ $sub->cnama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- JENIS --}}
                    <div class="mb-2">
                        <label>Jenis Asset</label>
                        <input type="text" id="jenisAsset" class="form-control" readonly>
                    </div>

                    {{-- QR FIELD --}}
                    <div id="fieldQr" style="display:none">
                        <div class="mb-2">
                            <label>Tanggal Beli</label>
                            <input type="date" name="dbeli" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Harga Beli</label>
                            <input type="number" name="nbeli" class="form-control" min="0">
                        </div>
                    </div>

                    {{-- NON QR FIELD --}}
                    <div id="fieldNonQr" style="display:none">
                        <div class="mb-2">
                            <label>Qty</label>
                            <input type="number" name="nqty" class="form-control" min="1">
                        </div>

                        <div class="mb-2">
                            <label>Min Stok</label>
                            <input type="number" name="nminstok" class="form-control" min="0">
                        </div>

                        <div class="mb-2">
                            <label>Satuan</label>
                            <select name="msatuan_id" class="form-control">
                                <option value="">-- Pilih Satuan --</option>
                                @foreach ($satuan as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-2">
                        <label>Catatan</label>
                        <textarea name="ccatatan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-warning">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/asset.js') }}"></script>
