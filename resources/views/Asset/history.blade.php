@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color:#6c757d;color:white;">
                <strong>Transaksi Asset Histori</strong>

                {{-- BUTTON PILIH BULAN --}}
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#monthFilterModal">
                    Pilih Bulan
                </button>
            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-sm align-middle">
                    <thead class="text-center table-dark">
                        <tr>
                            <th>Jenis Transaksi</th>
                            <th>Tanggal</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($historyByMonth as $bulan => $items)
                            {{-- HEADER BULAN --}}
                            <tr class="table-secondary fw-bold">
                                <td colspan="6">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                                </td>
                            </tr>

                            {{-- DATA PER BULAN --}}
                            @foreach ($items as $row)
                                @php
                                    $jenis = strtoupper($row->jenis_transaksi);

                                    $badgeClass = match ($jenis) {
                                        'PENAMBAHAN' => 'bg-success',
                                        'MUTASI' => 'bg-info',
                                        'PERBAIKAN' => 'bg-warning text-dark',
                                        'PEMUSNAHAN' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <tr>
                                    {{-- JENIS TRANSAKSI --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $jenis }}
                                        </span>
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i') }}
                                    </td>

                                    {{-- KODE BARANG --}}
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            {{ $row->kode_barang }}
                                        </span>
                                    </td>

                                    {{-- NAMA BARANG --}}
                                    <td>{{ $row->nama_barang }}</td>

                                    {{-- QTY --}}
                                    <td class="text-center">{{ $row->qty }}</td>

                                    {{-- CATATAN --}}
                                    <td>{{ $row->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Data histori transaksi belum tersedia
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- MODAL FILTER BULAN --}}
    {{-- ========================= --}}
    <div class="modal fade" id="monthFilterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Pilih Bulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Bulan</label>
                    <input type="month" id="bulanFilter" class="form-control">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" onclick="resetMonthFilter()">
                        Reset
                    </button>

                    <button class="btn btn-success" data-bs-dismiss="modal" onclick="applyMonthFilter()">
                        Terapkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- JS FILTER BULAN --}}
    {{-- ========================= --}}
    <script>
        function applyMonthFilter() {
            const bulan = document.getElementById('bulanFilter').value;
            if (!bulan) return;

            const url = new URL(window.location.href);
            url.searchParams.set('bulan', bulan);
            window.location.href = url.toString();
        }

        function resetMonthFilter() {
            const url = new URL(window.location.href);
            url.searchParams.delete('bulan');
            window.location.href = url.toString();
        }
    </script>
@endsection
