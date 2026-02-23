<table class="table table-bordered">
    <thead class="text-center">
        <tr>
            <th>Lokasi</th>
            <th>Kategori</th>
            <th>Sub Kategori</th>
            <th>Counter</th>
            <th>Kode Asset</th>
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

                <td class="text-center">
                    <span class="badge bg-success">
                        {{ $qr->cqr }}
                    </span>
                </td>

                {{-- Tanggal Beli --}}
                <td class="text-center">
                    {{ $qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-' }}
                </td>

                {{-- Harga Beli --}}
                <td class="text-center">
                    {{ $qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-' }}
                </td>

                <td class="text-center">
                    <span class="badge {{ strtolower($qr->cstatus) === 'aktif' ? 'bg-success' : 'bg-danger' }}">
                        {{ $qr->cstatus }}
                    </span>
                </td>

                <td>{{ $qr->ccatatan }}</td>
            </tr>
        @endforeach
    </tbody>

</table>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center">
    {{ $assetQr->links('pagination::bootstrap-5') }}
</div>
