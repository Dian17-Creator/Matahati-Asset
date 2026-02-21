{{-- ================= ASSET NON QR ================= --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset Non QR</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetNonQr">
            + Tambah Asset Non QR
        </button>
    </div>

    <div class="card-body">
        <div id="asset-nonqr-wrapper">
            @include('Asset.components.partials.asset_nonqr_table')
        </div>
    </div>
</div>
