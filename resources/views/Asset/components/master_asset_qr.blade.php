<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Asset QR</span>

        {{-- <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetQr">
            + Tambah Asset QR
        </button> --}}
    </div>

    <div class="card-body">
        <div id="asset-qr-wrapper">
            @include('Asset.components.partials.asset_qr_table')
        </div>
    </div>
</div>
