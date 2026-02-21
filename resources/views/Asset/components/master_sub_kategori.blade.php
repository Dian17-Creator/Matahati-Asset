<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Sub Kategori</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori"
            onclick="openCreateSubKategori()">
            + Tambah Sub Kategori
        </button>
    </div>

    <div class="card-body">
        <div id="subkategori-wrapper">
            @include('Asset.components.partials.subkategori_table')
        </div>
    </div>
</div>
