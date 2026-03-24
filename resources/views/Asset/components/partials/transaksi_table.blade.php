<div class="table-scroll">
    <table id="assetTable" class="table table-bordered table-sm">
        <thead class="text-center">
            <tr>
                <th class="sortable" data-sort="lokasi">Lokasi ↕</th>
                <th class="sortable" data-sort="tanggal">Tanggal Transaksi ↕</th>
                <th>Nomor Transaksi</th>
                <th class="sortable" data-sort="jenis">Jenis Transaksi ↕</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th class="sortable" data-sort="kode">Kode Asset ↕</th>
                <th class="sortable" data-sort="nama">Nama Asset ↕</th>
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
                        'movein' => 'bg-success', // 🟢 masuk
                        'moveout' => 'bg-danger', // 🔴 keluar
                        'serviceout' => 'bg-primary',
                        'servicein' => 'bg-warning text-dark',
                        'dispose' => 'bg-dark',
                        default => 'bg-secondary',
                    };

                    $labelJenis = match ($jenis) {
                        'add' => 'Penambahan',
                        'movein' => 'Mutasi Masuk',
                        'moveout' => 'Mutasi Keluar',
                        'serviceout' => 'Perbaikan Selesai',
                        'servicein' => 'Perbaikan Masuk',
                        'dispose' => 'Pemusnahan',
                        default => strtoupper($row->cjnstrans),
                    };
                @endphp

                <tr>
                    <td>{{ $row->department->cname ?? '-' }}</td>

                    <td class="text-center">
                        {{ $row->dtrans ? $row->dtrans->format('d-m-Y') : '-' }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-dark">
                            {{ $row->cnotrans }}
                        </span>
                    </td>

                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            {{ $labelJenis }}
                        </span>
                    </td>

                    <td>{{ $row->subKategori->kategori->cnama ?? '-' }}</td>
                    <td>{{ $row->subKategori->cnama ?? '-' }}</td>

                    <td class="text-center">
                        <span class="badge bg-primary">
                            {{ $row->ckode }}
                        </span>
                    </td>

                    <td>{{ $row->cnama ?? '-' }}</td>
                    <td>{{ $row->cmerk ?? '-' }}</td>

                    <td class="text-center">
                        {{ $row->nqty ?? 1 }}
                    </td>

                    <td class="text-center">
                        {{ $row->dbeli ? \Carbon\Carbon::parse($row->dbeli)->format('d-m-Y') : '-' }}
                    </td>

                    <td class="text-center">
                        {{ $row->dgaransi ? \Carbon\Carbon::parse($row->dgaransi)->format('d-m-Y') : '-' }}
                    </td>

                    <td class="text-center">
                        {{ $row->nhrgbeli ? 'Rp ' . number_format($row->nhrgbeli, 0, ',', '.') : '-' }}
                    </td>

                    <td>{{ $row->ccatatan ?? '-' }}</td>

                    <td class="text-center">
                        @if ($row->dreffoto)
                            <a href="{{ asset('uploads/transaksi/' . $row->dreffoto) }}" target="_blank">
                                <img src="{{ asset('uploads/transaksi/' . $row->dreffoto) }}"
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
    th.sortable {
        cursor: pointer;
        user-select: none;
    }

    .table-scroll {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .table-scroll table {
        min-width: 2000px;
    }

    .table-scroll thead th,
    .table-scroll tbody td {
        padding: 12px;
        text-align: center;
        vertical-align: middle;
    }
</style>
