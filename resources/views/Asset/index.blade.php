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

        @include('Asset.components.master_asset_transaksi')

    </div>

    @include('Asset.modal.modal_satuan')

    @include('Asset.modal.modal_kategori')

    @include('Asset.modal.modal_sub_kategori')

    @include('Asset.modal.modal_asset')

    @include('Asset.modal.modal_asset_qr')

    @include('Asset.modal.modal_asset_nonqr')

    @include('Asset.modal.modal_asset_pemusnahan')

    <script>
        window.routeMsatuanStore = "{{ route('msatuan.store') }}";
    </script>

    <script>
        window.routeGenerateKode = "{{ route('asset.trans.generate') }}";
    </script>

    <script src="{{ asset('js/asset.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/asset.css') }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const debug = sessionStorage.getItem('debug_pemusnahan');

            if (debug) {
                const data = JSON.parse(debug);

                console.group('%c[DEBUG PEMUSNAHAN ASSET]', 'color:red;font-weight:bold');
                console.table(data);

                Object.entries(data).forEach(([key, value]) => {
                    if (value === null || value === '') {
                        console.warn(`⚠ FIELD KOSONG: ${key}`);
                    }
                });

                console.groupEnd();

                // OPTIONAL: hapus setelah tampil
                sessionStorage.removeItem('debug_pemusnahan');
            }
        });
    </script>
@endsection
