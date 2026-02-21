<table class="table table-bordered table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>Kategori</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th width="200">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($subkategori as $sub)
            <tr>
                <td>{{ $sub->kategori->cnama }}</td>
                <td class="text-center">{{ $sub->ckode }}</td>
                <td>{{ $sub->cnama }}</td>
                <td class="text-center">
                    @if ($sub->fqr)
                        <span class="badge bg-primary">QR</span>
                    @else
                        <span class="badge bg-secondary">Non QR</span>
                    @endif
                </td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSubKategori"
                        onclick="openEditSubKategori(
                            {{ $sub->nid }},
                            {{ $sub->nidkat }},
                            '{{ $sub->ckode }}',
                            '{{ $sub->cnama }}',
                            {{ $sub->fqr ? 1 : 0 }}
                        )">
                        Edit
                    </button>

                    <form method="POST" action="{{ route('asset.subkategori.destroy', $sub->nid) }}" class="d-inline"
                        onsubmit="return confirm('Hapus sub kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Data tidak tersedia
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $subkategori->links('pagination::bootstrap-5') }}
</div>
