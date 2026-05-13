<div class="modal fade" id="modalTambahReminder" tabindex="-1" aria-labelledby="modalTambahReminderLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formTambahReminder">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #198754; color: white;">
                    <h5 class="modal-title" id="modalTambahReminderLabel">Tambah Reminder Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Asset <span class="text-danger">*</span></label>
                        <select class="form-select" name="asset_type" id="reminder_asset_type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="QR">QR</option>
                            <option value="NOQR">NO QR</option>
                        </select>
                    </div>

                    <div class="mb-3" id="reminder_asset_group" style="display: none;">
                        <label class="form-label fw-bold">Pilih Asset <span class="text-danger">*</span></label>
                        <select class="form-select" id="reminder_asset_select">
                            <option value="">-- Pilih Tipe Dulu --</option>
                        </select>
                        <input type="hidden" name="asset_qr_id" id="reminder_asset_qr_id">
                        <input type="hidden" name="asset_noqr_code" id="reminder_asset_noqr_code">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Reminder <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="reminder_date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea class="form-control" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assetTypeSelect = document.getElementById('reminder_asset_type');
    const assetGroup = document.getElementById('reminder_asset_group');
    const assetSelect = document.getElementById('reminder_asset_select');
    const qrIdInput = document.getElementById('reminder_asset_qr_id');
    const noqrCodeInput = document.getElementById('reminder_asset_noqr_code');

    let allAssets = [];

    // Load assets only once when modal is shown
    const modal = document.getElementById('modalTambahReminder');
    modal.addEventListener('shown.bs.modal', function() {
        if (allAssets.length === 0) {
            assetSelect.innerHTML = '<option value="">Loading...</option>';
            assetSelect.disabled = true;

            fetch(`{{ url('asset/get-asset-by-status') }}?status=Aktif`)
                .then(res => res.json())
                .then(data => {
                    allAssets = data;
                    assetSelect.innerHTML = '<option value="">-- Pilih Asset --</option>';
                    assetSelect.disabled = false;
                })
                .catch(() => {
                    assetSelect.innerHTML = '<option value="">Gagal load</option>';
                });
        }
    });

    modal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('formTambahReminder').reset();
        assetGroup.style.display = 'none';
        qrIdInput.value = '';
        noqrCodeInput.value = '';
    });

    assetTypeSelect.addEventListener('change', function() {
        const type = this.value;
        if (type) {
            assetGroup.style.display = 'block';
            assetSelect.required = true;
            
            // Filter assets based on type
            let html = '<option value="">-- Pilih Asset --</option>';
            const targetJenis = type === 'QR' ? 'QR' : 'NOQR';
            
            allAssets.filter(a => a.jenis === targetJenis).forEach(item => {
                let lokasi = item.lokasi ?? '-';
                let label = `[${item.jenis}] ${lokasi} - ${item.kode} - ${item.nama}`;
                // For QR we need ID, for NOQR we need KODE
                let value = type === 'QR' ? item.id : item.kode;
                html += `<option value="${value}">${label}</option>`;
            });
            
            assetSelect.innerHTML = html;
        } else {
            assetGroup.style.display = 'none';
            assetSelect.required = false;
        }
    });

    assetSelect.addEventListener('change', function() {
        const type = assetTypeSelect.value;
        if (type === 'QR') {
            qrIdInput.value = this.value;
            noqrCodeInput.value = '';
        } else if (type === 'NOQR') {
            qrIdInput.value = '';
            noqrCodeInput.value = this.value;
        }
    });

    document.getElementById('formTambahReminder').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`{{ route('asset.reminder.store') }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const bsModal = bootstrap.Modal.getInstance(modal);
                bsModal.hide();
                if(typeof window.loadReminder === 'function') {
                    window.loadReminder();
                }
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan sistem');
        });
    });
});
</script>
