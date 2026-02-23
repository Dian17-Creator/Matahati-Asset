<div class="d-flex justify-content-end gap-2 mb-2">
    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalAssetPemusnahan">
        Pemusnahan Asset
    </button>

    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAssetTrans">
        + Tambah Transaksi
    </button>
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
