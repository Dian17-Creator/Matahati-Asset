@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center mb-2">
        <h4 class="mb-0">KARTU STOK BARANG</h4>
    </div>

    <p class="d-flex justify-content-center align-items-center gap-2 mb-4">
        Periode :
        <strong>{{ \Carbon\Carbon::parse($start)->format('d M Y') }}</strong>
        s/d
        <strong>{{ \Carbon\Carbon::parse($end)->format('d M Y') }}</strong>
    </p>

    {{-- 🔍 FILTER --}}
    <form method="GET" id="filterForm" class="mb-3">
        <div class="row g-2 align-items-center">

            {{-- 🔍 SEARCH --}}
            <div class="col">
                <div class="input-group">
                    <input type="text" id="searchInput" name="search" class="form-control"
                        placeholder="Cari kode / nama produk / satuan..." value="{{ request('search') }}">

                    <span class="input-group-text bg-success text-white">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </div>

            <div class="col-md-2">
                <select name="lokasi" class="form-control lokasi-select" onchange="this.form.submit()">
                    <option value="">📍 Semua Lokasi</option>
                    @foreach ($lokasiList as $lok)
                        <option value="{{ $lok->nid }}" {{ request('lokasi') == $lok->nid ? 'selected' : '' }}>
                            {{ $lok->cname }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔥 HIDDEN DATE --}}
            <input type="hidden" id="start_date" name="start_date" value="{{ $start }}">
            <input type="hidden" id="end_date" name="end_date" value="{{ $end }}">

            {{-- Tombol tanggal --}}
            <div class="col-auto">
                <button type="button" class="btn btn-outline-success" style="height:38px;" data-bs-toggle="modal"
                    data-bs-target="#dateFilterModal">
                    📅 Pilih Tanggal
                </button>
            </div>

        </div>
    </form>

    {{-- MODAL --}}
    @include('kartustok.modal.modal_filter_date')

    {{-- TABLE --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background-color: #B63352; color: white;">
            <b>Kartu Stok</b>
        </div>

        <div class="card-body">
            <div class="container">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Satuan</th>
                                <th>Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Akhir</th>
                                <th>Min Stok</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td class="text-center">{{ $row->kode_produk }}</td>
                                    <td>{{ $row->nama_produk }}</td>
                                    <td class="text-center">{{ $row->satuan }}</td>

                                    <td class="text-center">{{ number_format($row->awal) }}</td>
                                    <td class="text-center text-success">{{ number_format($row->masuk) }}</td>
                                    <td class="text-center text-danger">{{ number_format($row->keluar) }}</td>

                                    <td class="text-center fw-bold">{{ number_format($row->akhir) }}</td>
                                    <td class="text-center">{{ $row->min_stok }}</td>

                                    <td class="text-center">
                                        <a href="?start_date={{ $start }}&end_date={{ $end }}&kode={{ $row->kode_produk }}"
                                            class="btn btn-sm btn-success">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .lokasi-select {
            height: 38px;
            border-radius: 8px;
        }

        /* focus effect biar sama tema */
        .lokasi-select:focus {
            border-color: #008211;
            box-shadow: 0 0 0 0.2rem rgba(0, 130, 17, 0.25);
            outline: none;
        }
    </style>

    @include('kartustok.components.detail')

    {{-- JS --}}
    <script src="{{ asset('js/kartustok.js') }}"></script>
@endsection
