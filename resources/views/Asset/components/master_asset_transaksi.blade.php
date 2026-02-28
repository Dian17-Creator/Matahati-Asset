<div class="d-flex justify-content-between align-items-center mb-2">

    {{-- FILTER JENIS TRANSAKSI --}}
    <div class="d-flex gap-2 flex-wrap">

        {{-- Filter Jenis --}}
        <select id="filterJenisTransaksi" class="form-select form-select-sm" style="width: 170px;">
            <option value="">Semua Jenis</option>
            <option value="Penambahan">Penambahan</option>
            <option value="Mutasi">Mutasi</option>
            <option value="Perbaikan Masuk">Perbaikan Masuk</option>
            <option value="Perbaikan Selesai">Perbaikan Selesai</option>
            <option value="Pemusnahan">Pemusnahan</option>
        </select>

        {{-- Filter Kategori --}}
        <select id="filterKategori" class="form-select form-select-sm" style="width: 160px;">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriFilter as $kat)
                <option value="{{ $kat->cnama }}">{{ $kat->cnama }}</option>
            @endforeach
        </select>

        {{-- Filter Sub Kategori --}}
        <select id="filterSubKategori" class="form-select form-select-sm" style="width: 160px;">
            <option value="">Semua Sub Kategori</option>
            @foreach ($subKategoriFilter as $sub)
                <option value="{{ $sub->cnama }}">{{ $sub->cnama }}</option>
            @endforeach
        </select>
    </div>

    {{-- TOMBOL AKSI --}}
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalAssetPemusnahan">
            Pemusnahan Asset
        </button>

        <button class="btn btn-sm btn-warning" style="color: white;" data-bs-toggle="modal"
            data-bs-target="#modalAssetPerbaikan">
            Perbaikan Asset
        </button>

        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAssetMutasi">
            Mutasi Asset
        </button>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetTrans">
            + Tambah Transaksi
        </button>
    </div>
</div>

<div class="card mb-4">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">
        <span>Transaksi Asset</span>
    </div>

    {{-- BODY --}}
    <div class="card-body">

        <div id="transaksi-wrapper">
            @include('Asset.components.partials.transaksi_table')
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const filterJenis = document.getElementById('filterJenisTransaksi');
        const filterKategori = document.getElementById('filterKategori');
        const filterSubKategori = document.getElementById('filterSubKategori');

        function loadTransaksi(page = 1) {
            const params = new URLSearchParams({
                page: page,
                jenis: filterJenis?.value || '',
                kategori: filterKategori?.value || '',
                subkategori: filterSubKategori?.value || '',
            });

            fetch(`{{ route('asset.transaksi.ajax') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('transaksi-wrapper').innerHTML = html;
                    bindPagination(); // 🔥 WAJIB
                });
        }

        function bindPagination() {
            document.querySelectorAll('#transaksi-wrapper .pagination a')
                .forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = new URL(this.href).searchParams.get('page');
                        loadTransaksi(page);
                    });
                });
        }

        // === EVENT FILTER ===
        filterJenis?.addEventListener('change', () => loadTransaksi());
        filterKategori?.addEventListener('change', () => loadTransaksi());
        filterSubKategori?.addEventListener('change', () => loadTransaksi());

        // First bind pagination (page load)
        bindPagination();
    });
</script>
