@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center mb-4">
        <h4 class="mb-0">DATA AUDIT ASSET</h4>
    </div>

    {{-- 🔍 FILTER & SEARCH --}}
    <form method="GET" id="auditFilterForm" action="{{ route('audit.index') }}" class="mb-3">
        <div class="row g-2 align-items-center">

            {{-- SEARCH --}}
            <div class="col">
                <div class="input-group">
                    <input type="text" id="auditSearchInput" name="search" class="form-control"
                        placeholder="Cari kode / nama asset..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            {{-- FILTER STATUS --}}
            <div class="col-md-2">
                <select id="auditStatusFilter" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="BAIK/SESUAI" {{ request('status') == 'BAIK/SESUAI' ? 'selected' : '' }}>
                        BAIK / SESUAI
                    </option>
                    <option value="MASALAH/TDK.SESUAI"
                        {{ request('status') == 'MASALAH/TDK.SESUAI' ? 'selected' : '' }}>
                        MASALAH / TDK. SESUAI
                    </option>
                </select>
            </div>

            {{-- FILTER LOKASI --}}
            <div class="col-md-2">
                <select id="auditLokasiFilter" name="lokasi" class="form-select">
                    <option value="">📍 Semua Lokasi</option>
                    @foreach ($lokasiList as $lok)
                        <option value="{{ $lok->nid }}" {{ request('lokasi') == $lok->nid ? 'selected' : '' }}>
                            {{ $lok->cname }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
    </form>

    {{-- TABLE WRAPPER WITH LOADING OVERLAY --}}
    <div id="audit-table-wrapper" class="position-relative">
        
        {{-- Spinner Overlay --}}
        <div id="audit-loading-spinner" class="d-none position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center" style="z-index: 1000; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(2px); transition: all 0.3s;">
            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Table Content --}}
        <div id="audit-table-content" style="transition: opacity 0.2s ease-in-out;">
            @include('audit.partials.audit_table')
        </div>

    </div>

    {{-- Link modern styling shared across Category & Transaction pages --}}
    <link rel="stylesheet" href="{{ asset('css/asset.css') }}?v=1">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('auditFilterForm');
            const searchInput = document.getElementById('auditSearchInput');
            const statusFilter = document.getElementById('auditStatusFilter');
            const lokasiFilter = document.getElementById('auditLokasiFilter');
            
            const spinner = document.getElementById('audit-loading-spinner');
            const content = document.getElementById('audit-table-content');

            let searchTimeout = null;

            // Load audit via AJAX
            function loadAudit(page = 1) {
                // Show loading spinner and dim table
                spinner.classList.remove('d-none');
                content.style.opacity = '0.4';

                const params = new URLSearchParams({
                    page: page,
                    search: searchInput.value,
                    status: statusFilter.value,
                    lokasi: lokasiFilter.value
                });

                fetch(`{{ route('audit.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        content.innerHTML = html;
                        bindPagination();
                        
                        // Hide loading spinner and restore opacity
                        spinner.classList.add('d-none');
                        content.style.opacity = '1';
                    })
                    .catch(err => {
                        console.error(err);
                        spinner.classList.add('d-none');
                        content.style.opacity = '1';
                    });
            }

            // Bind click event to pagination links
            function bindPagination() {
                content.querySelectorAll('.pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;
                        loadAudit(page);
                    });
                });
            }

            // Form Submit Interceptor
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(searchTimeout);
                loadAudit(1);
            });

            // Instant Search (Debounced)
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadAudit(1);
                }, 400); // 400ms debounce
            });

            // Dropdown filters
            statusFilter.addEventListener('change', () => loadAudit(1));
            lokasiFilter.addEventListener('change', () => loadAudit(1));

            // Init pagination binding
            bindPagination();
        });
    </script>
@endsection
