{{-- ================= MODAL KATEGORI ================= --}}
<div class="modal fade" id="modalKategori">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.kategori.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodKategori" value="POST">

            <div class="modal-content">

                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5>Tambah Kategori</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Kode</label>
                        <input type="text" name="ckode" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="cnama" class="form-control" required>
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
