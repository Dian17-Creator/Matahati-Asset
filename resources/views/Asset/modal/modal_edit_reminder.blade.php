<!-- Modal Edit Reminder -->
<div class="modal fade" id="modalEditReminder" tabindex="-1" aria-labelledby="modalEditReminderLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditReminder">
            <input type="hidden" id="edit_reminder_id">

            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditReminderLabel">Edit Reminder Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Tipe Asset -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Tipe Asset <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="asset_type" id="edit_asset_type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="QR">QR</option>
                            <option value="NOQR">NO QR</option>
                        </select>
                    </div>

                    <!-- Asset -->
                    <div class="mb-3" id="edit_asset_group" style="display:none;">
                        <label class="form-label fw-bold">
                            Pilih Asset <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" id="edit_asset_select">
                            <option value="">-- Pilih Asset --</option>
                        </select>

                        <input type="hidden" name="asset_qr_id" id="edit_asset_qr_id">
                        <input type="hidden" name="asset_noqr_code" id="edit_asset_noqr_code">
                    </div>

                    <!-- Tanggal -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Tanggal Reminder <span class="text-danger">*</span>
                        </label>

                        <input type="date" class="form-control" name="reminder_date" id="edit_reminder_date" required>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>

                        <textarea class="form-control" name="note" id="edit_note" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-warning">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const editModal = document.getElementById('modalEditReminder');

        const editAssetType = document.getElementById('edit_asset_type');
        const editAssetGroup = document.getElementById('edit_asset_group');
        const editAssetSelect = document.getElementById('edit_asset_select');

        const editQrId = document.getElementById('edit_asset_qr_id');
        const editNoqrCode = document.getElementById('edit_asset_noqr_code');

        let allAssetsEdit = [];

        // Load asset sekali
        editModal.addEventListener('shown.bs.modal', function() {

            if (allAssetsEdit.length === 0) {

                editAssetSelect.innerHTML = '<option>Loading...</option>';

                fetch(`{{ url('asset/get-asset-by-status') }}?status=Aktif`)
                    .then(res => res.json())
                    .then(data => {

                        allAssetsEdit = data;
                    });
            }
        });

        // Change tipe asset
        editAssetType.addEventListener('change', function() {

            const type = this.value;

            if (!type) {
                editAssetGroup.style.display = 'none';
                return;
            }

            editAssetGroup.style.display = 'block';

            let html = '<option value="">-- Pilih Asset --</option>';

            const targetJenis = type;

            allAssetsEdit.filter(a => a.jenis === targetJenis).forEach(item => {

                let lokasi = item.lokasi ?? '-';

                let label =
                    `[${item.jenis}] ${lokasi} - ${item.kode} - ${item.nama}`;

                let value =
                    type === 'QR' ?
                    item.id :
                    item.kode;

                html += `
                    <option value="${value}">
                        ${label}
                    </option>
                `;
            });

            editAssetSelect.innerHTML = html;
        });

        // Change asset
        editAssetSelect.addEventListener('change', function() {

            const type = editAssetType.value;

            if (type === 'QR') {

                editQrId.value = this.value;
                editNoqrCode.value = '';

            } else {

                editQrId.value = '';
                editNoqrCode.value = this.value;
            }
        });

        // Function buka modal edit
        window.editReminder = function(id) {

            fetch(`/asset/reminder/${id}`)
                .then(res => res.json())
                .then(reminderRes => {

                    if (!reminderRes.success) {
                        alert(reminderRes.message);
                        return;
                    }

                    const data = reminderRes.data;

                    // ambil asset dulu
                    fetch(`{{ url('asset/get-asset-by-status') }}?status=Aktif`)
                        .then(res => res.json())
                        .then(allAssets => {
                            allAssetsEdit = allAssets; // Sinkronkan cache

                            // set id
                            document.getElementById('edit_reminder_id').value =
                                data.id;

                            // set type
                            editAssetType.value =
                                data.asset_type;

                            // show asset group
                            editAssetGroup.style.display = 'block';

                            // build dropdown
                            let html =
                                '<option value="">-- Pilih Asset --</option>';

                            const targetJenis = data.asset_type;

                            allAssets
                                .filter(a => a.jenis === targetJenis)
                                .forEach(item => {

                                    let lokasi =
                                        item.lokasi ?? '-';

                                    let label =
                                        `[${item.jenis}] ${lokasi} - ${item.kode} - ${item.nama}`;

                                    let value =
                                        data.asset_type === 'QR' ?
                                        item.id :
                                        item.kode;

                                    let selected = '';

                                    if (
                                        data.asset_type === 'QR' &&
                                        String(data.asset_qr_id) === String(value)
                                    ) {
                                        selected = 'selected';
                                    }

                                    if (
                                        data.asset_type === 'NOQR' &&
                                        String(data.asset_noqr_code) === String(value)
                                    ) {
                                        selected = 'selected';
                                    }

                                    html += `
                                <option value="${value}" ${selected}>
                                    ${label}
                                </option>
                            `;
                                });

                            editAssetSelect.innerHTML = html;

                            // hidden field
                            if (data.asset_type === 'QR') {

                                editQrId.value =
                                    data.asset_qr_id;

                                editNoqrCode.value = '';

                            } else {

                                editQrId.value = '';

                                editNoqrCode.value =
                                    data.asset_noqr_code;
                            }

                            // tanggal
                            document.getElementById('edit_reminder_date').value =
                                data.reminder_date.substring(0, 10);

                            // note
                            document.getElementById('edit_note').value =
                                data.note ?? '';

                            // tampilkan modal
                            const bsModal =
                                new bootstrap.Modal(editModal);

                            bsModal.show();

                        });

                })
                .catch(err => {

                    console.error(err);

                    alert('Gagal load data reminder');
                });
        }

        // Submit edit
        document.getElementById('formEditReminder')
            .addEventListener('submit', function(e) {

                e.preventDefault();

                const id =
                    document.getElementById('edit_reminder_id').value;

                const formData =
                    new FormData(this);

                formData.append('_method', 'PUT');

                fetch(`/asset/reminder/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {

                        if (res.success) {

                            alert(res.message);

                            bootstrap.Modal
                                .getInstance(editModal)
                                .hide();

                            if (typeof window.loadReminder === 'function') {
                                window.loadReminder();
                            }

                        } else {

                            alert(res.message || 'Terjadi kesalahan');
                        }

                    })
                    .catch(err => {

                        console.error(err);

                        alert('Terjadi kesalahan sistem');
                    });

            });

    });
</script>