<div class="modal fade" id="modalAssetTrans">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.trans.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header" style="background-color:#B63352;color:white;">
                    <h5>Tambah Transaksi Asset</h5>
                </div>

                <div class="modal-body">

                    {{-- LOKASI (AUTO DARI ASSET) --}}
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

                    {{-- KODE ASSET --}}
                    <div class="mb-2">
                        <label>Kode Asset</label>
                        <select name="ckode_asset" id="assetSelect" class="form-control" required>
                            <option value="">-- Pilih Kode Asset --</option>

                            @foreach ($assetDropdown as $a)
                                <option value="{{ $a['kode'] }}" data-lokasi="{{ $a['niddept'] }}"
                                    data-qty="{{ $a['qty'] }}" data-jenis="{{ $a['jenis'] }}">
                                    [{{ $a['jenis'] }}]
                                    {{ $a['kode'] }} — {{ $a['nama'] }}
                                    ({{ $a['lokasi'] }} | Qty: {{ $a['qty'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- QTY --}}
                    <div class="mb-2">
                        <label>Qty</label>
                        <input type="number" name="nqty" id="qtyInput" class="form-control" min="1"
                            value="1" required>
                    </div>

                    {{-- MERK --}}
                    <div class="mb-2">
                        <label>Merk</label>
                        <input type="text" name="cmerk" class="form-control">
                    </div>

                    {{-- TANGGAL BELI & GARANSI --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Beli</label>
                            <input type="date" name="dbeli" class="form-control" value="{{ date('Y-m-d') }}">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector('#modalAssetTrans form');

        form.addEventListener('submit', function() {
            const data = new FormData(form);

            const payload = {};
            data.forEach((v, k) => payload[k] = v);

            console.group('🚨 SUBMIT TRANSAKSI ASSET');
            console.table(payload);
            console.groupEnd();

            // SIMPAN KE LOCALSTORAGE (BERTAHAN SAAT REFRESH)
            localStorage.setItem('last_asset_trans_submit', JSON.stringify(payload));
        });

        // TAMPILKAN LAGI SETELAH REFRESH
        const last = localStorage.getItem('last_asset_trans_submit');
        if (last) {
            console.group('♻️ LAST SUBMIT (AFTER REFRESH)');
            console.table(JSON.parse(last));
            console.groupEnd();
        }

    });
</script>
