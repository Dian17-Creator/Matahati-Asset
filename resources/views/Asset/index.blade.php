@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="mb-0">DASHBOARD ASSET</h4>
        </div>


        <div class="row">

            {{-- MASTER SATUAN --}}
            <div class="col-md-6 mb-4">
                @include('Asset.components.master_satuan')
            </div>

            {{-- MASTER KATEGORI --}}
            <div class="col-md-6 mb-4">
                @include('Asset.components.master_kategori')
            </div>

        </div>


        {{-- ================= SUB KATEGORI ================= --}}

        @include('Asset.components.master_sub_kategori')

        @include('Asset.components.master_asset_qr')

        @include('Asset.components.master_asset_nonqr')

    </div>

    @include('Asset.modal.modal_satuan')

    @include('Asset.modal.modal_kategori')

    @include('Asset.modal.modal_sub_kategori')

    @include('Asset.modal.modal_asset')

    @include('Asset.modal.modal_asset_qr')

    @include('Asset.modal.modal_asset_nonqr')

    <script>
        window.routeMsatuanStore = "{{ route('msatuan.store') }}";
    </script>

    <script src="{{ asset('js/asset.js') }}"></script>
@endsection
