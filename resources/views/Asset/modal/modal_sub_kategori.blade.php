{{-- ================= MODAL SUB KATEGORI ================= --}}
<div class="modal fade" id="modalSubKategori">
    <div class="modal-dialog">
        <form method="POST" id="formSubKategori" action="{{ route('asset.subkategori.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodSubKategori" value="POST">

            <div class="modal-content">
                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5 id="titleSubKategori">Tambah Sub Kategori</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Kategori</label>
                        <select name="nidkat" class="form-control" required>
                            @foreach ($kategoriAll as $kat)
                                <option value="{{ $kat->nid }}">{{ $kat->cnama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Kode</label>
                        <input type="text" name="ckode" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="cnama" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Jenis Asset</label>
                        <select name="fqr" class="form-control">
                            <option value="1">QR</option>
                            <option value="0">Non QR</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/asset.js') }}"></script>
