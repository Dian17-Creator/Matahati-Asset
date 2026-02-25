<div class="table-scroll">
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
</div>

<style>
    .table-scroll {
        max-height: 400px;
        /* tinggi area scroll */
        overflow-y: auto;
        /* scroll vertikal */
        overflow-x: auto;
        /* scroll horizontal */
        position: relative;
    }

    .table-scroll table {
        min-width: 1500px;
        /* paksa biar bisa scroll horizontal */
        table-layout: auto;
    }

    /* THEAD */
    .table-scroll thead th {
        padding: 12px;
        /* atas-bawah | kiri-kanan */
        text-align: center;
        vertical-align: middle;
    }

    /* TBODY */
    .table-scroll tbody td {
        padding: 12px;
        vertical-align: middle;
    }
</style>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center">
    {{ $assetNoQr->links('pagination::bootstrap-5') }}
</div>
