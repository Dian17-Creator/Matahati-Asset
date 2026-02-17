@extends('layouts.app')

@section('content')
    <div class="container">

        <h4 class="mb-3">Master Asset</h4>

        {{-- BUTTON --}}
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                + Kategori
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori"
                onclick="openCreateSubKategori()">
                + Sub Kategori
            </button>

            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAsset">
                + Asset
            </button>
        </div>

        {{-- ================= MASTER SATUAN ================= --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Master Satuan</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSatuan"
                    onclick="openCreateSatuan()">
                    + Tambah Satuan
                </button>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($satuan as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalSatuan"
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
            </div>
        </div>


        {{-- ================= KATEGORI ================= --}}
        <div class="card mb-4">
            <div class="card-header">Kategori</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $kat)
                            <tr>
                                <td>{{ $kat->ckode }}</td>
                                <td>{{ $kat->cnama }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalKategori"
                                        onclick="openEditKategori({{ $kat->nid }}, '{{ $kat->ckode }}', '{{ $kat->cnama }}')">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('asset.kategori.destroy', $kat->nid) }}"
                                        class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        {{-- ================= SUB KATEGORI ================= --}}
        <div class="card mb-4">
            <div class="card-header">Sub Kategori</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th width="200">Aksi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subkategori as $sub)
                            <tr>
                                <td>{{ $sub->kategori->cnama }}</td>
                                <td>{{ $sub->ckode }}</td>
                                <td>{{ $sub->cnama }}</td>
                                <td>
                                    @if ($sub->fqr)
                                        <span class="badge bg-primary">QR</span>
                                    @else
                                        <span class="badge bg-secondary">Non QR</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalSubKategori"
                                        onclick="openEditSubKategori(
                                            {{ $sub->nid }},
                                            {{ $sub->nidkat }},
                                            '{{ $sub->ckode }}',
                                            '{{ $sub->cnama }}',
                                            {{ $sub->fqr }}
                                        )">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('asset.subkategori.destroy', $sub->nid) }}"
                                        class="d-inline" onsubmit="return confirm('Hapus sub kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= ASSET QR ================= --}}
        <div class="card mb-4">
            <div class="card-header">Asset QR</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>
                            <th>Counter</th>
                            <th>QR Code</th>
                            <th>Tgl Beli</th>
                            <th>Harga Beli</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assetQr as $qr)
                            <tr>
                                <td>{{ $qr->department->cname }}</td>
                                <td>{{ $qr->subKategori->kategori->cnama }}</td>
                                <td>{{ $qr->subKategori->cnama }}</td>
                                <td>{{ $qr->nurut }}</td>
                                <td>{{ $qr->cqr }}</td>

                                {{-- Tanggal Beli --}}
                                <td>
                                    {{ $qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-' }}
                                </td>

                                {{-- Harga Beli --}}
                                <td>
                                    {{ $qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-' }}
                                </td>

                                <td>{{ $qr->cstatus }}</td>
                                <td>{{ $qr->ccatatan }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        {{-- ================= ASSET NON QR ================= --}}
        <div class="card mb-4">
            <div class="card-header">Asset Non QR</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>
                            <th>Qty</th>
                            <th>Min Stok</th>
                            <th>Satuan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assetNoQr as $nqr)
                            <tr>
                                <td>{{ $nqr->department->cname }}</td>
                                <td>{{ $nqr->subKategori->kategori->cnama }}</td>
                                <td>{{ $nqr->subKategori->cnama }}</td>
                                <td>{{ $nqr->nqty }}</td>
                                <td>{{ $nqr->nminstok }}</td>
                                <td>{{ $nqr->satuan?->nama ?? '-' }}</td>
                                <td>{{ $nqr->ccatatan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ================= MODAL SATUAN ================= --}}
    <div class="modal fade" id="modalSatuan">
        <div class="modal-dialog">
            <form method="POST" id="formSatuan">
                @csrf
                <input type="hidden" name="_method" id="methodSatuan" value="POST">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="titleSatuan">Tambah Satuan</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama Satuan</label>
                            <input type="text" name="nama" id="namaSatuan" class="form-control"
                                placeholder="Pcs / Unit / Set" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary" id="btnSatuan">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- ================= MODAL KATEGORI ================= --}}
    <div class="modal fade" id="modalKategori">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('asset.kategori.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodKategori" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Kategori</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Kode</label>
                            <input type="text" name="ckode" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="cnama" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL SUB KATEGORI ================= --}}
    <div class="modal fade" id="modalSubKategori">
        <div class="modal-dialog">
            <form method="POST" id="formSubKategori" action="{{ route('asset.subkategori.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodSubKategori" value="POST">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="titleSubKategori">Tambah Sub Kategori</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Kategori</label>
                            <select name="nidkat" class="form-control" required>
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->nid }}">{{ $kat->cnama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Kode</label>
                            <input type="text" name="ckode" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="cnama" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Jenis Asset</label>
                            <select name="fqr" class="form-control">
                                <option value="1">QR</option>
                                <option value="0">Non QR</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- ================= MODAL ASSET ================= --}}
    <div class="modal fade" id="modalAsset">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('asset.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Asset</h5>
                    </div>

                    <div class="modal-body">

                        {{-- DEPARTMENT --}}
                        <div class="mb-2">
                            <label>Lokasi</label>
                            <select name="niddept" class="form-control" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->nid }}">{{ $dept->cname }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- KATEGORI --}}
                        <div class="mb-2">
                            <label>Kategori</label>
                            <select id="filterKategori" class="form-control">
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->nid }}">{{ $kat->cnama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SUB KATEGORI --}}
                        <div class="mb-2">
                            <label>Sub Kategori</label>
                            <select name="nidsubkat" id="filterSubKategori" class="form-control" required>
                                @foreach ($subkategori as $sub)
                                    <option value="{{ $sub->nid }}" data-kat="{{ $sub->nidkat }}"
                                        data-fqr="{{ $sub->fqr }}">
                                        {{ $sub->cnama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- JENIS --}}
                        <div class="mb-2">
                            <label>Jenis Asset</label>
                            <input type="text" id="jenisAsset" class="form-control" readonly>
                        </div>

                        {{-- QR FIELD --}}
                        <div id="fieldQr" style="display:none">
                            <div class="mb-2">
                                <label>Tanggal Beli</label>
                                <input type="date" name="dbeli" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Harga Beli</label>
                                <input type="number" name="nbeli" class="form-control" min="0">
                            </div>
                        </div>

                        {{-- NON QR FIELD --}}
                        <div id="fieldNonQr" style="display:none">
                            <div class="mb-2">
                                <label>Qty</label>
                                <input type="number" name="nqty" class="form-control" min="1">
                            </div>

                            <div class="mb-2">
                                <label>Min Stok</label>
                                <input type="number" name="nminstok" class="form-control" min="0">
                            </div>

                            <div class="mb-2">
                                <label>Satuan</label>
                                <select name="msatuan_id" class="form-control">
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach ($satuan as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- CATATAN --}}
                        <div class="mb-2">
                            <label>Catatan</label>
                            <textarea name="ccatatan" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-warning">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.routeMsatuanStore = "{{ route('msatuan.store') }}";
    </script>

    <script src="{{ asset('js/asset.js') }}"></script>
@endsection
