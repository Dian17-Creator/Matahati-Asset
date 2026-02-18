<table class="table table-bordered table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($satuan as $i => $s)
            <tr>
                <td class="text-center">{{ $satuan->firstItem() + $i }}</td>
                <td>{{ $s->nama }}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSatuan"
                        onclick="openEditSatuan({{ $s->id }}, '{{ $s->nama }}')">
                        Edit
                    </button>

                    <form method="POST" action="{{ route('msatuan.destroy', $s->id) }}" class="d-inline"
                        onsubmit="return confirm('Hapus satuan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $satuan->links('pagination::bootstrap-5') }}
</div>
