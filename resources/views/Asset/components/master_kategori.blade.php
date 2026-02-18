<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Kategori</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalKategori">
            + Tambah Kategori
        </button>
    </div>

    <div class="card-body">
        {{-- AJAX WRAPPER --}}
        <div id="kategori-wrapper">
            @include('Asset.components.partials.kategori_table')
        </div>
    </div>
</div>
