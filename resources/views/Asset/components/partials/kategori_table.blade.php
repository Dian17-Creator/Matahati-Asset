<table class="table table-bordered table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kategori as $i => $kat)
            <tr>
                <td class="text-center">{{ $kat->ckode }}</td>
                <td>{{ $kat->cnama }}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori"
                        onclick="openEditKategori({{ $kat->nid }}, '{{ $kat->ckode }}', '{{ $kat->cnama }}')">
                        Edit
                    </button>

                    <form method="POST" action="{{ route('asset.kategori.destroy', $kat->nid) }}" class="d-inline"
                        onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center">
    {{ $kategori->links('pagination::bootstrap-5') }}
</div>
