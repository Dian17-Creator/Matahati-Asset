<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel Backoffice') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}?v=3">

    <!-- ✅ INLINE STYLE UNTUK OVERRIDE PASTI - TAMBAHKAN INI -->
    <style>
        /* FORCE STYLING MODAL NOTIFIKASI DI MOBILE */
        @media (max-width: 768px) {
            #notifModal .modal-dialog {
                width: 100vw !important;
                max-width: 100vw !important;
                height: 100vh !important;
                margin: 0 !important;
            }

            #notifModal .modal-content {
                height: 100vh !important;
                border-radius: 0 !important;
            }

            #notifModal .modal-header {
                padding: 1.5rem 1.25rem !important;
                min-height: 80px !important;
            }

            #notifModal .modal-title {
                font-size: 1.8rem !important;
                font-weight: 700 !important;
            }

            #notifModal .btn-close {
                width: 2.5rem !important;
                height: 2.5rem !important;
                opacity: 1 !important;
            }

            #notifModal .modal-body {
                max-height: calc(100vh - 80px) !important;
                padding: 1.25rem !important;
                font-size: 1.2rem !important;
            }

            #notifModal .list-group-item {
                padding: 1.5rem 1.25rem !important;
                font-size: 1.15rem !important;
                margin-bottom: 0.75rem !important;
                border-radius: 12px !important;
                min-height: 90px !important;
            }

            #notifModal .list-group-item .fw-bold,
            #notifModal .list-group-item div.fw-bold {
                font-size: 1.3rem !important;
                font-weight: 700 !important;
                line-height: 1.5 !important;
            }

            #notifModal .list-group-item small {
                font-size: 1.05rem !important;
            }
        }
    </style>
</head>

<body>
    {{-- Navbar utama --}}
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #B63352;">
        <div class="container d-flex align-items-center">

            {{-- tombol toggler mobile di KIRI: buka offcanvas dari kiri --}}
            <button class="navbar-toggler d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="d-flex align-items-center mx-auto mx-lg-0 gap-2">

                <a class="navbar-brand fw-bold mb-0" href="{{ route('asset.index') }}">
                    Matahati Asset
                </a>

                {{-- 🔔 NOTIFIKASI MOBILE --}}
                <a href="#" class="position-relative text-white d-lg-none" data-bs-toggle="modal"
                    data-bs-target="#notifModal" id="notifButtonMobileTop">

                    <i class="bi bi-bell-fill mobile-bell-icon"></i>

                    <span id="notifBadgeMobileTop"
                        class="position-absolute top-0 start-100 translate-middle
                     badge rounded-pill bg-danger"
                        style="font-size:0.65rem; display:none;">
                        0
                    </span>
                </a>
            </div>


            {{-- Versi desktop: tampilkan menu horizontal hanya di >= lg --}}
            <div class="d-none d-lg-flex w-100 justify-content-end">
                <ul class="navbar-nav ms-auto align-items-center">

                    {{-- Menu HOME --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('backoffice.index') ? 'fw-bold text-white' : '' }}"
                            href="{{ route('asset.index') }}">HOME</a>
                    </li>

                    <div class="nav-divider"></div>

                    {{-- Tombol Logout --}}
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Offcanvas (untuk mobile: muncul dari KIRI) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel">
            <!-- OFFCANVAS HEADER (REPLACE EXISTING) -->
            <div class="offcanvas-header"
                style="background-color: #B63352; color: #fff; display:flex; align-items:center; padding:0 1rem;">
                <div class="container">
                    <button id="internalToggler" type="button" class="btn btn-link p-0 me-3 offcanvas-hamburger"
                        data-bs-dismiss="offcanvas" aria-label="Tutup menu" style="color: white;">
                        <!-- gunakan bootstrap icon agar selalu tampil -->
                        <i style="font-size: 100px;" class="bi bi-list" aria-hidden="true"></i>
                    </button>
                </div>

            </div>
            <div class="offcanvas-body d-flex flex-column p-3">

                <div class="container">

                    <nav class="menu-list">

                        <a href="{{ route('asset.index') }}"
                            class="menu-item {{ request()->routeIs('backoffice.index') ? 'active' : '' }}">
                            Home
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="logout-btn-fixed">Logout</button>
                        </form>

                    </nav>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Popup Notifikasi --}}
    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">
                <div class="modal-header" style="background-color: #B63352; color: white;">
                    <h5 class="modal-title" id="notifModalLabel">Notifikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body" id="notifBody">
                    <p class="text-center text-muted">Memuat notifikasi...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- konten halaman --}}
    <div class="container mt-4">
        @yield('content')
    </div>

    @stack('scripts')

    <!-- JQUERY (WAJIB, SEBELUM asset.js) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FILE JS KAMU -->
    <script src="{{ asset('js/asset.js') }}"></script>

</body>

</html>
