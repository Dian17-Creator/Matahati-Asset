<div class="table-scroll">
    <table class="table table-bordered table-sm">
        <thead class="text-center">
            <tr>
                <th>No</th>
                <th>Lokasi</th>
                <th>Tanggal Transaksi</th>
                <th>Nomor Transaksi</th>
                <th>Jenis Transaksi</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Kode Asset</th>
                <th>Nama Asset</th>
                <th>Merk</th>
                <th>Qty</th>
                <th>Tgl Beli</th>
                <th>Garansi</th>
                <th>Harga</th>
                <th>Catatan</th>
                <th>Foto</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($transaksi as $row)
                @php
                    $jenis = strtolower($row->cjnstrans);

                    $badgeClass = match ($jenis) {
                        'add' => 'bg-success',
                        'move' => 'bg-info',
                        'serviceout' => 'bg-primary',
                        'servicein' => 'bg-warning text-dark',
                        'dispose' => 'bg-danger',
                        default => 'bg-secondary',
                    };

                    $labelJenis = match ($jenis) {
                        'add' => 'Penambahan',
                        'move' => 'Mutasi',
                        'serviceout' => 'Perbaikan Selesai',
                        'servicein' => 'Perbaikan Masuk',
                        'dispose' => 'Pemusnahan',
                        default => strtoupper($row->cjnstrans),
                    };
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    {{-- Lokasi --}}
                    <td>{{ $row->department->cname ?? '-' }}</td>

                    {{-- Tanggal Transaksi --}}
                    <td class="text-center">
                        {{ $row->dtrans ? $row->dtrans->format('d-m-Y') : '-' }}
                    </td>

                    {{-- Nomor Transaksi --}}
                    <td class="text-center">
                        <span class="badge bg-dark">
                            {{ $row->cnotrans }}
                        </span>
                    </td>

                    {{-- Jenis Transaksi --}}
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            {{ $labelJenis }}
                        </span>
                    </td>

                    {{-- Kategori --}}
                    <td>{{ $row->subKategori->kategori->cnama ?? '-' }}</td>

                    {{-- Sub Kategori --}}
                    <td>{{ $row->subKategori->cnama ?? '-' }}</td>

                    {{-- Kode Asset --}}
                    <td class="text-center">
                        <span class="badge bg-primary">
                            {{ $row->ckode }}
                        </span>
                    </td>

                    {{-- Nama Asset --}}
                    <td>{{ $row->cnama ?? '-' }}</td>

                    {{-- Merk --}}
                    <td>{{ $row->cmerk ?? '-' }}</td>

                    {{-- Qty --}}
                    <td class="text-center">
                        {{ $row->nqty ?? 1 }}
                    </td>

                    {{-- Tanggal Beli --}}
                    <td class="text-center">
                        {{ $row->dbeli ? \Carbon\Carbon::parse($row->dbeli)->format('d-m-Y') : '-' }}
                    </td>

                    {{-- Garansi --}}
                    <td class="text-center">
                        {{ $row->dgaransi ? \Carbon\Carbon::parse($row->dgaransi)->format('d-m-Y') : '-' }}
                    </td>

                    {{-- Harga --}}
                    <td class="text-center">
                        {{ $row->nhrgbeli ? 'Rp ' . number_format($row->nhrgbeli, 0, ',', '.') : '-' }}
                    </td>

                    {{-- Catatan --}}
                    <td>{{ $row->ccatatan ?? '-' }}</td>

                    {{-- Foto --}}
                    <td class="text-center">
                        @if ($row->dreffoto)
                            <a href="{{ asset('uploads/asset/' . $row->dreffoto) }}" target="_blank">
                                <img src="{{ asset('uploads/asset/' . $row->dreffoto) }}" alt="Foto Asset"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center text-muted">
                        Belum ada transaksi asset
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<div class="d-flex justify-content-center mt-2">
    {{ $transaksi->links('pagination::bootstrap-5') }}
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
        min-width: 2000px;
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
