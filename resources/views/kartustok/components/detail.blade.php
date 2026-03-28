@if (isset($trans) && count($trans) > 0)

    <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #B63352;">
            <b>Detail Kartu Stok</b>
        </div>

        <div class="card-body">

            <div class="mb-3">
                <strong>Kode :</strong> {{ $kode ?? '-' }} <br>
                <strong>Nama Barang :</strong> {{ $nama_barang ?? '-' }} <br>
                <strong>Periode :</strong> {{ $start }} s/d {{ $end }}
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No Transaksi</th>
                            <th>Keterangan</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr style="background:#f8f9fa;">
                            <td colspan="6"><b>Stok Awal</b></td>
                            <td class="text-center"><b>{{ $stok_awal }}</b></td>
                        </tr>

                        @foreach ($trans as $i => $t)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($t->dtrans)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $t->no_trans ?? '-' }}</td>
                                <td>{{ $t->keterangan ?? '-' }}</td>

                                <td class="text-center text-success">
                                    {{ $t->masuk > 0 ? number_format($t->masuk) : '-' }}
                                </td>

                                <td class="text-center text-danger">
                                    {{ $t->keluar ? number_format($t->keluar) : '-' }}
                                </td>

                                <td class="text-center">
                                    <b>{{ number_format($t->saldo) }}</b>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endif
