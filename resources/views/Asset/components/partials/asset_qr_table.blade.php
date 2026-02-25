<div class="table-scroll">
    <table class="table table-bordered table-sm">
        <thead class="text-center">
            <tr>
                <th>Lokasi</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Nama Asset</th>
                <th>Counter</th>
                <th>Kode Asset</th>
                <th>Tgl Beli</th>
                <th>Tgl Transaksi</th>
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
                    <td>{{ $qr->cnama }}</td>
                    <td class="text-center">
                        {{ str_pad($qr->nurut, 4, '0', STR_PAD_LEFT) }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-success">
                            {{ $qr->cqr }}
                        </span>
                    </td>

                    {{-- Tanggal Beli --}}
                    <td class="text-center">
                        {{ $qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-' }}
                    </td>

                    {{-- Tanggal Transaksi --}}
                    <td class="text-center">
                        {{ $qr->dtrans ? \Carbon\Carbon::parse($qr->dtrans)->format('d-m-Y') : '-' }}
                    </td>

                    {{-- Harga Beli --}}
                    <td class="text-center">
                        {{ $qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-' }}
                    </td>

                    <td class="text-center">
                        @php
                            $status = strtolower($qr->cstatus);
                            $badgeClass = match ($status) {
                                'aktif' => 'bg-success',
                                'perbaikan' => 'bg-warning text-dark',
                                'non aktif' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $badgeClass }}">
                            {{ $qr->cstatus }}
                        </span>
                    </td>

                    <td>{{ $qr->ccatatan }}</td>
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
    {{ $assetQr->links('pagination::bootstrap-5') }}
</div>
