<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset QR</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetQr">
            + Tambah Asset QR
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Sub Kategori</th>
                    <th>Counter</th>
                    <th>QR Code</th>
                    <th>Tgl Beli</th>
                    <th>Harga Beli</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assetQr as $qr)
                    <tr>
                        <td>{{ $qr->department->cname }}</td>
                        <td>{{ $qr->subKategori->kategori->cnama }}</td>
                        <td>{{ $qr->subKategori->cnama }}</td>
                        <td class="text-center">{{ $qr->nurut }}</td>
                        <td class="text-center">{{ $qr->cqr }}</td>

                        {{-- Tanggal Beli --}}
                        <td class="text-center">
                            {{ $qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-' }}
                        </td>

                        {{-- Harga Beli --}}
                        <td class="text-center">
                            {{ $qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-' }}
                        </td>

                        <td>{{ $qr->cstatus }}</td>
                        <td>{{ $qr->ccatatan }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
