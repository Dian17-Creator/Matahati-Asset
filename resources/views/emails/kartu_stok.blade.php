<h2>Kartu Stok Harian</h2>
<p>Tanggal: {{ $date }}</p>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Awal</th>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Akhir</th>
        <th>Min</th>
    </tr>

    @foreach ($data as $item)
        <tr>
            <td>{{ $item->kode_produk }}</td>
            <td>{{ $item->nama_produk }}</td>
            <td>{{ $item->awal }}</td>
            <td>{{ $item->masuk }}</td>
            <td>{{ $item->keluar }}</td>
            <td><b>{{ $item->akhir }}</b></td>
            <td>{{ $item->min_stok }}</td>
        </tr>
    @endforeach
</table>
