{{-- ================= MODAL SATUAN ================= --}}
<div class="modal fade" id="modalSatuan">
    <div class="modal-dialog">
        <form method="POST" id="formSatuan">
            @csrf
            <input type="hidden" name="_method" id="methodSatuan" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="titleSatuan">Tambah Satuan</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama Satuan</label>
                        <input type="text" name="nama" id="namaSatuan" class="form-control"
                            placeholder="Pcs / Unit / Set" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="btnSatuan">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    window.routeMsatuanStore = "{{ route('msatuan.store') }}";
</script>

<script src="{{ asset('js/asset.js') }}"></script>
