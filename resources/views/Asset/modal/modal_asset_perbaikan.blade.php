<div class="modal fade" id="modalAssetPerbaikan" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asset.qr.perbaikan') }}">
            @csrf

            {{-- 🔥 hidden --}}
            <input type="hidden" name="jenis_asset" id="jenisAssetPerbaikan">
            <input type="hidden" name="nidsubkat" id="subkatInput">
            <input type="hidden" name="niddept" id="deptInput">

            <div class="modal-content">

                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Perbaikan Asset</h5>
                </div>

                <div class="modal-body">

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Set Status</label>
                        <select name="cstatus" id="statusSelect" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Perbaikan">Perbaikan</option>
                            <option value="Aktif">Aktif</option>
                        </select>
                    </div>

                    {{-- ASSET --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Asset</label>
                        <select name="kode_asset" id="assetSelect" class="form-select" required disabled>
                            <option value="">-- Pilih Status Dulu --</option>
                        </select>
                    </div>

                    {{-- QTY --}}
                    <div class="mb-3 d-none" id="qtyWrapper">
                        <label class="form-label fw-bold">Qty Perbaikan</label>
                        <input type="text" name="qty" id="qtyInput" class="form-control" min="1">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="dtrans" class="form-control" required>
                    </div>

                    {{-- CATATAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="ccatatan" class="form-control" rows="2"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        Simpan Perbaikan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const statusSelect = document.getElementById('statusSelect');
        const assetSelect = document.getElementById('assetSelect');
        const jenisInput = document.getElementById('jenisAssetPerbaikan');
        const subkatInput = document.getElementById('subkatInput');
        const deptInput = document.getElementById('deptInput');

        const qtyWrapper = document.getElementById('qtyWrapper');
        const qtyInput = document.getElementById('qtyInput');
        const form = document.querySelector('#modalAssetPerbaikan form');
        const modal = document.getElementById('modalAssetPerbaikan');

        // =========================
        // LOAD ASSET
        // =========================
        statusSelect.addEventListener('change', function() {

            const status = this.value;

            assetSelect.innerHTML = '<option value="">Loading...</option>';
            assetSelect.disabled = true;

            fetch(`{{ url('asset/get-asset-by-status') }}?status=${status}`)
                .then(res => res.json())
                .then(data => {

                    let html = '<option value="">-- Pilih Asset --</option>';

                    data.forEach(item => {

                        let lokasi = item.lokasi ?? '-';

                        let label =
                            `[${item.jenis}] ${lokasi} - ${item.kode} - ${item.nama}`;

                        let extra = '';

                        if (item.jenis === 'NON_QR') {

                            // 🔵 kalau ada sisa → perbaikan selesai
                            if (item.sisa !== undefined) {
                                extra = `(Sisa: ${item.sisa})`;
                            }

                            // 🟡 kalau tidak ada sisa → perbaikan masuk
                            else if (item.qty !== undefined) {
                                extra = `(Stok: ${item.qty})`;
                            }
                        }

                        html += `<option
                            value="${item.jenis}|${item.id}"
                            data-jenis="${item.jenis}"
                            data-subkat="${item.nidsubkat}"
                            data-dept="${item.niddept}"
                            data-sisa="${item.sisa ?? ''}"
                        >
                            ${label} ${extra}
                        </option>`;
                    });

                    assetSelect.innerHTML = html;
                    assetSelect.disabled = false;
                })
                .catch(() => {
                    assetSelect.innerHTML = '<option value="">Gagal load</option>';
                });
        });

        // =========================
        // PILIH ASSET
        // =========================
        assetSelect.addEventListener('change', function() {

            const selected = this.options[this.selectedIndex];

            if (!selected) return;

            const jenis = selected.dataset.jenis;
            const subkat = selected.dataset.subkat;
            const dept = selected.dataset.dept;

            // 🔥 SET SEMUA
            jenisInput.value = jenis || '';
            subkatInput.value = subkat || '';
            deptInput.value = dept || '';

            // UI
            if (jenis === 'NON_QR') {
                qtyWrapper.classList.remove('d-none');
                qtyInput.required = true;
            } else {
                qtyWrapper.classList.add('d-none');
                qtyInput.required = false;
                qtyInput.value = '';
            }

            console.log('🔥 SELECTED:', {
                jenis,
                subkat,
                dept
            });
        });

        // =========================
        // SUBMIT VALIDATION
        // =========================
        form.addEventListener('submit', function(e) {

            if (!jenisInput.value) {
                e.preventDefault();
                alert('Jenis asset kosong!');
                return;
            }

            if (!subkatInput.value) {
                e.preventDefault();
                alert('Sub kategori tidak terbaca!');
                return;
            }

            if (!deptInput.value) {
                e.preventDefault();
                alert('Departemen tidak terbaca!');
                return;
            }

            if (jenisInput.value === 'NON_QR' && !qtyInput.value) {
                e.preventDefault();
                alert('Qty wajib diisi!');
                return;
            }

            console.log('🔥 FINAL SUBMIT:', {
                jenis: jenisInput.value,
                subkat: subkatInput.value,
                dept: deptInput.value,
                qty: qtyInput.value
            });
        });

        // =========================
        // RESET MODAL
        // =========================
        modal.addEventListener('shown.bs.modal', function() {

            statusSelect.value = '';
            jenisInput.value = '';
            subkatInput.value = '';
            deptInput.value = '';

            assetSelect.innerHTML = '<option value="">-- Pilih Status Dulu --</option>';
            assetSelect.disabled = true;

            qtyWrapper.classList.add('d-none');
            qtyInput.value = '';
        });

    });
</script>
