{{-- ================= ASSET NON QR ================= --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset Non QR</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetNonQr">
            + Tambah Asset Non QR
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Sub Kategori</th>
                    <th>Qty</th>
                    <th>Min Stok</th>
                    <th>Satuan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assetNoQr as $nqr)
                    <tr>
                        <td>{{ $nqr->department->cname }}</td>
                        <td>{{ $nqr->subKategori->kategori->cnama }}</td>
                        <td>{{ $nqr->subKategori->cnama }}</td>
                        <td class="text-center">{{ $nqr->nqty }}</td>
                        <td class="text-center">{{ $nqr->nminstok }}</td>
                        <td>{{ $nqr->satuan?->nama ?? '-' }}</td>
                        <td>{{ $nqr->ccatatan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
