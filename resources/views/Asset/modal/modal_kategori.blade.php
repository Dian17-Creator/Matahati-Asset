{{-- ================= MODAL KATEGORI ================= --}}
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('asset.kategori.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodKategori" value="POST">

                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5 id="titleKategori">Tambah Kategori</h5>
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

                <div class="modal-footer d-flex justify-content-between w-100 gap-2">
                    <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success flex-fill">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/asset.js') }}"></script>