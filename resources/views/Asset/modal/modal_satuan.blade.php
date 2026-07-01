<div class="modal fade" id="modalSatuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formSatuan" action="{{ route('msatuan.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodSatuan" value="POST">

                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5 id="titleSatuan">Tambah Satuan</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Satuan</label>
                        <input type="text" name="nama" id="namaSatuan" class="form-control"
                            placeholder="Pcs / Unit / Set" required>
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