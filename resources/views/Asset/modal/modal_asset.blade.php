<div class="modal fade" id="modalAssetTrans">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.trans.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header" style="background-color:#B63352;color:white;">
                    <h5 class="modal-title">Tambah Transaksi Asset</h5>
                </div>

                <div class="modal-body">

                    {{-- ================= --}}
                    {{-- JENIS ASSET --}}
                    {{-- ================= --}}
                    <div class="mb-2">
                        <label>Jenis Asset</label>
                        <select name="jenis_asset" id="jenisAsset" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="QR">QR</option>
                            <option value="NON_QR">Non QR</option>
                        </select>
                    </div>

                    <hr>

                    {{-- ================= --}}
                    {{-- FORM QR --}}
                    {{-- ================= --}}
                    <div id="formQR" style="display:none">

                        <div class="row gx-2">
                            {{-- LOKASI --}}
                            <div class="col-md-6 mb-3">
                                <label>Lokasi</label>
                                <select name="nlokasi" class="form-control">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->nid }}">{{ $dept->cname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SUB KATEGORI --}}
                            <div class="col-md-6 mb-3">
                                <label>Sub Kategori</label>
                                <select name="sub_kategori_id" class="form-control">
                                    <option value="">-- Pilih Sub Kategori --</option>
                                    @foreach ($subkategoriAll as $s)
                                        @if ($s->fqr == 1)
                                            <option value="{{ $s->nid }}">
                                                {{ $s->kategori->ckode }} - {{ $s->ckode }} | {{ $s->cnama }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- NAMA ASSET & MERK --}}
                        <div class="row gx-2">
                            <div class="col-md-6 mb-2">
                                <label>Nama Asset</label>
                                <input type="text" name="nama_asset" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>Merk</label>
                                <input type="text" name="cmerk" class="form-control">
                            </div>
                        </div>

                    </div>

                    {{-- ================= --}}
                    {{-- FORM NON QR --}}
                    {{-- ================= --}}
                    <div id="formNonQR" style="display:none">

                        {{-- KODE ASSET --}}
                        <div class="mb-2">
                            <label>Kode Asset (Non QR)</label>
                            <select name="ckode_asset_nonqr" class="form-control">
                                <option value="">-- Pilih Kode Asset --</option>
                                @foreach ($assetDropdown as $a)
                                    @if ($a['jenis'] === 'NON_QR')
                                        <option value="{{ $a['kode'] }}">
                                            {{ $a['kode'] }} — {{ $a['nama'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- QTY --}}
                        <div class="mb-2">
                            <label>Qty</label>
                            <input type="number" name="nqty" class="form-control" value="1" min="1">
                        </div>

                    </div>

                    {{-- ========================= --}}
                    {{-- FORM BERSAMA --}}
                    {{-- ========================= --}}
                    <div id="formCommon" style="display:none">

                        {{-- TANGGAL --}}
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Tanggal Beli</label>
                                <input type="date" name="dbeli" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>Garansi Sampai</label>
                                <input type="date" name="dgaransi" class="form-control">
                            </div>
                        </div>

                        {{-- HARGA --}}
                        <div class="mb-2">
                            <label>Harga Beli</label>
                            <input type="text" name="nhrgbeli" class="form-control">
                        </div>

                        {{-- CATATAN (DIPINDAH KE BAWAH) --}}
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

                </div>

                {{-- FOOTER --}}
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

{{-- ================= --}}
{{-- JAVASCRIPT --}}
{{-- ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const jenis = document.getElementById('jenisAsset');
        const formQR = document.getElementById('formQR');
        const formNonQR = document.getElementById('formNonQR');
        const formCommon = document.getElementById('formCommon');

        jenis.addEventListener('change', function() {

            formQR.style.display = 'none';
            formNonQR.style.display = 'none';
            formCommon.style.display = 'none';

            if (this.value === 'QR') {
                formQR.style.display = 'block';
                formCommon.style.display = 'block';
            }

            if (this.value === 'NON_QR') {
                formNonQR.style.display = 'block';
                formCommon.style.display = 'block';
            }
        });
    });
</script>
