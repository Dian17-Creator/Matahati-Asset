@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center mb-2">
        <h4 class="mb-0">KARTU STOK BARANG</h4>
    </div>
    <p class="d-flex justify-content-center align-items-center gap-2 mb-4">
        Periode :
        <strong> {{ \Carbon\Carbon::parse($start)->format('d M Y') }} </strong>
        s/d
        <strong> {{ \Carbon\Carbon::parse($end)->format('d M Y') }} </strong>
    </p>

    {{-- 🔍 FILTER --}}
    <form method="GET" class="mb-3">
        <div style="display:flex; gap:10px; align-items:center;">
            <input type="date" name="start_date" value="{{ $start }}" class="form-control">

            {{-- <div class="col-auto">
                            <div class="date-sync-icon" onclick="syncLogDate()" title="Samakan tanggal">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                        </div> --}}

            <input type="date" name="end_date" value="{{ $end }}" class="form-control">

            <button type="submit" class="btn btn-success" style="width: 175px;">
                Filter
            </button>
        </div>
    </form>

    <div class="card mb-4">
        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background-color: #B63352; color: white;">
            <span>Kartu Stok</span>
        </div>

        {{-- BODY --}}
        <div class="card-body">

            <div class="container">

                {{-- 📊 TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr style="text-align: center; ">
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Satuan</th>
                                <th>Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Akhir</th>
                                <th>Min Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td style="text-align: center;">{{ $row->kode_produk }}</td>
                                    <td>{{ $row->nama_produk }}</td>
                                    <td style="text-align: center;">{{ $row->satuan }}</td>

                                    <td style="text-align:center;">
                                        {{ number_format($row->awal) }}
                                    </td>

                                    <td style="text-align:center; color:green;">
                                        {{ number_format($row->masuk) }}
                                    </td>

                                    <td style="text-align:center; color:red;">
                                        {{ number_format($row->keluar) }}
                                    </td>

                                    <td style="text-align:center; font-weight:bold;">
                                        {{ number_format($row->akhir) }}
                                    </td>
                                    <td style="text-align: center;">{{ $row->min_stok }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center;">
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

    {{-- <script>
        function syncLogDate() {
            const start = document.getElementById("start_date");
            const end = document.getElementById("end_date");

            if (start && end) {
                end.value = start.value;
            }
        }
    </script> --}}
@endsection
