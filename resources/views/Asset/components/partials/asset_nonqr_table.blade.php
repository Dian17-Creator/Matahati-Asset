<table class="table table-bordered">
    <thead class="text-center">
        <tr>
            <th>Lokasi</th>
            <th>Kategori</th>
            <th>Sub Kategori</th>
            <th>Nama Asset</th>
            <th>Kode Asset</th>
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

                <td>{{ $nqr->cnama }}</td>
                <td class="text-center">
                    <span class="badge bg-success">
                        {{ $nqr->ckode }}
                    </span>
                </td>

                <td class="text-center">{{ $nqr->nqty }}</td>
                <td class="text-center">{{ $nqr->nminstok }}</td>
                <td>{{ $nqr->satuan?->nama ?? '-' }}</td>
                <td>{{ $nqr->ccatatan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center">
    {{ $assetNoQr->links('pagination::bootstrap-5') }}
</div>
