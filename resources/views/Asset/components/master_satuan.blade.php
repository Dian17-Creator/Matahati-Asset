<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Satuan</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSatuan">
            + Tambah Satuan
        </button>
    </div>


    <div class="card-body">
        {{-- AJAX WRAPPER --}}
        <div id="satuan-wrapper">
            @include('Asset.components.partials.satuan_table')
        </div>
    </div>
</div>
