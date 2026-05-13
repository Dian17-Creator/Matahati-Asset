<div class="d-flex justify-content-between align-items-center mb-2 mt-4">
    <div class="d-flex gap-2 flex-wrap">
        {{-- Filter Asset Type --}}
        <select id="filterAssetTypeReminder" class="form-select form-select-sm" style="width: 155px;">
            <option value="">Semua Tipe Asset</option>
            <option value="QR">QR</option>
            <option value="NOQR">NO QR</option>
        </select>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahReminder">
            + Tambah Reminder
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">
        <b>Asset Reminder</b>
    </div>

    <div class="card-body">
        <div id="reminder-wrapper">
            @include('Asset.components.partials.reminder_table')
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterAssetTypeReminder = document.getElementById('filterAssetTypeReminder');

        window.loadReminder = function() {
            const params = new URLSearchParams({
                asset_type: filterAssetTypeReminder?.value || '',
            });

            fetch(`{{ route('asset.reminder.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        let html = '';
                        if (response.data.length === 0) {
                            html = `<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>`;
                        } else {
                            response.data.forEach((item, index) => {
                                const assetCode = item.asset_type === 'QR' ? item.asset_qr_id : item.asset_noqr_code;
                                const assetName = item.asset_name || '-';
                                const dateObj = new Date(item.reminder_date);
                                const formattedDate = dateObj.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });

                                html += `
                                    <tr>
                                        <td class="text-center align-middle">${index + 1}</td>
                                        <td class="text-center align-middle">${item.asset_type}</td>
                                        <td class="text-center align-middle">${assetCode || '-'} - ${assetName}</td>
                                        <td class="text-center align-middle">${formattedDate}</td>
                                        <td class="text-center align-middle">${item.note || '-'}</td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-danger btn-delete-reminder" data-id="${item.id}">Hapus</button>
                                            <button class="btn btn-warning btn-sm"
                                            onclick="editReminder(${item.id})">
                                            Edit
                                        </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        document.querySelector('#reminder-wrapper tbody').innerHTML = html;
                        bindDeleteReminder();
                    }
                });
        }

        function bindDeleteReminder() {
            document.querySelectorAll('.btn-delete-reminder').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin menghapus reminder ini?')) {
                        const id = this.dataset.id;
                        const formData = new FormData();
                        formData.append('_method', 'DELETE');

                        fetch(`{{ url('asset/reminder') }}/${id}`, {
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
                                    loadReminder();
                                } else {
                                    alert(data.message || 'Gagal menghapus');
                                }
                            });
                    }
                });
            });
        }

        filterAssetTypeReminder?.addEventListener('change', () => loadReminder());

        loadReminder();
    });
</script>