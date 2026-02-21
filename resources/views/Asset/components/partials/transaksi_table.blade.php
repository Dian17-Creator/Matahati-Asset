<table class="table table-bordered table-sm">
    <thead class="text-center">
        <tr>
            <th>No</th>
            <th>Lokasi</th>
            <th>Kategori</th>
            <th>Sub Kategori</th>
            <th>Kode Asset</th>
            <th>Nama Asset</th>
            <th>Merk</th>
            <th>Tgl Beli</th>
            <th>Garansi</th>
            <th>Harga</th>
            <th>Catatan</th>
            <th>Foto</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($transaksi as $row)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>

                {{-- Lokasi / Department --}}
                <td>{{ $row->department->cname ?? '-' }}</td>

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
                <td colspan="12" class="text-center text-muted">
                    Belum ada transaksi asset
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center">
    {{ $transaksi->links('pagination::bootstrap-5') }}
</div>
