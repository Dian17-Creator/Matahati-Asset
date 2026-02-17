@extends('layouts.app')

@section('content')
    <div class="container">

        <h4 class="mb-3">Master Asset</h4>

        {{-- BUTTON --}}
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                + Kategori
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori">
                + Sub Kategori
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAsset">
                + Asset
            </button>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $kat)
                            <tr>
                                <td>{{ $kat->ckode }}</td>
                                <td>{{ $kat->cnama }}</td>
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
                                <td>{{ $nqr->csatuan }}</td>
                                <td>{{ $nqr->ccatatan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ================= MODAL KATEGORI ================= --}}
    <div class="modal fade" id="modalKategori">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('asset.kategori.store') }}">
                @csrf
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL SUB KATEGORI ================= --}}
    <div class="modal fade" id="modalSubKategori">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('asset.subkategori.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Sub Kategori</h5>
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                            <label>Lokasi / Department</label>
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

                        {{-- CATATAN --}}
                        <div class="mb-2">
                            <label>Catatan</label>
                            <textarea name="ccatatan" class="form-control"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-warning">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= JS ================= --}}
    <script>
        const kategori = document.getElementById('filterKategori');
        const subkat = document.getElementById('filterSubKategori');
        const jenis = document.getElementById('jenisAsset');

        function filterSub() {
            const kat = kategori.value;
            [...subkat.options].forEach(opt => {
                opt.style.display = opt.dataset.kat == kat ? 'block' : 'none';
            });
            subkat.selectedIndex = [...subkat.options].findIndex(o => o.style.display === 'block');
            setJenis();
        }

        function setJenis() {
            const fqr = subkat.options[subkat.selectedIndex].dataset.fqr;
            jenis.value = fqr == 1 ? 'QR' : 'Non QR';
        }

        kategori.addEventListener('change', filterSub);
        subkat.addEventListener('change', setJenis);
        filterSub();
    </script>
@endsection
