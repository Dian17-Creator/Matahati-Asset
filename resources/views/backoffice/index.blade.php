@extends('layouts.app')

@php
    // === GUARD VARIABLE (WAJIB) ===
    $departments = $departments ?? collect();
    $rekenings = $rekenings ?? collect();
    $devices = $devices ?? collect();
    $admins = $admins ?? collect();
@endphp

@section('content')
    <div class="container mt-4">
        {{-- ====== HEADER ====== --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Dashboard Backoffice</h2>

            @if (auth()->user()->fhrd == 1)
                <button style="background-color: blue; padding: 7px; color: white;" class="btn btn-light btn-sm"
                    data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    + Tambah Departemen
                </button>
            @endif
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (auth()->user()->fhrd == 1)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Master Departemen</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($departments)
                                @forelse ($departments as $dept)
                                    <tr>
                                        <td>{{ $dept->nid }}</td>
                                        <td>{{ $dept->cname }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editDepartmentModal{{ $dept->nid }}">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteDepartmentModal{{ $dept->nid }}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- MODAL EDIT & DELETE --}}
                                    @include('backoffice.modal.modal_edit_department', ['dept' => $dept])
                                    @include('backoffice.modal.modal_delete_department', ['dept' => $dept])
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">Belum ada departemen</td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="3" class="text-muted">Data departemen tidak tersedia</td>
                                </tr>
                            @endisset
                        </tbody>

                    </table>
                </div>
            </div>
        @endif

        {{-- ====== TABEL USER ====== --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="position-relative flex-grow-1 me-3">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                    placeholder="Cari user (nama, email, cabang, role)...">
                <button id="clearSearch" class="btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2"
                    style="border: none; display:none;">✖</button>
            </div>

            @if (auth()->user()->fhrd == 1)
                <div class="d-inline-flex gap-2">
                    <button style="background-color: green" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                        + Tambah User
                    </button>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importFingerprintModal"
                        style="color: rgb(39, 39, 39)">
                        Import Fingerprint
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendSlipModal">
                        Kirim Slip Gaji
                    </button>
                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exportUserModal">
                        Export User
                    </button>
                </div>
            @endif
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header text-white" style="background-color: green">
                Daftar User
            </div>
            <div class="card-body">
                <div class="table-scroll">
                    <table id="userTable" class="table table-bordered align-middle text-center table-users">
                        <thead class="bg-light">
                            <tr>
                                <th>Username</th>
                                <th>Gmail</th>
                                <th>No Telepon</th>
                                <th>Nomor KTP</th>
                                <th>Nomor Rekening</th>
                                <th>Jenis Bank</th>
                                <th>Nama</th>
                                <th>Nama Lengkap</th>
                                <th>Finger ID</th> {{-- kolom baru --}}
                                <th>Tanggal Masuk</th>
                                <th class="sortable" data-column="cabang">
                                    Cabang <span class="sort-icon" id="sortIconCabang">↕</span>
                                </th>
                                <th class="sortable" data-column="role">
                                    Role <span class="sort-icon" id="sortIconRole">↕</span>
                                </th>
                                <th>Status</th>
                                <th>Absensi</th>
                                <th>Izin</th>
                                <th>Face</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($users)
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->cemail }}</td>
                                        <td>{{ $user->cmailaddress ?? '-' }}</td>
                                        <td>{{ $user->cphone ?? '-' }}</td>
                                        <td>{{ $user->cktp ?? '-' }}</td>

                                        <td>
                                            {{ $user->caccnumber ? preg_replace('/\s+/', '', $user->caccnumber) : $user->rekening->nomor_rekening ?? '-' }}
                                        </td>

                                        <td>
                                            @php
                                                $bankUser = trim((string) ($user->bank ?? ''));
                                                $bankRel = trim((string) ($user->rekening->bank ?? ''));
                                                $bankName =
                                                    $bankUser !== '' ? $bankUser : ($bankRel !== '' ? $bankRel : '');
                                                $atasNama = trim((string) ($user->rekening->atas_nama ?? ''));
                                            @endphp

                                            @if ($bankName === '')
                                                -
                                            @elseif (strtolower($bankName) === 'mandiri')
                                                {{ $atasNama !== '' ? 'MANDIRI - ' . $atasNama : 'MANDIRI' }}
                                            @else
                                                {{ strtoupper($bankName) }}
                                            @endif
                                        </td>

                                        <td>{{ $user->cname }}</td>
                                        <td>{{ $user->cfullname ?? '-' }}</td>
                                        <td>{{ $user->finger_id ?? '-' }}</td>
                                        <td>{{ $user->dtanggalmasuk ? \Carbon\Carbon::parse($user->dtanggalmasuk)->format('d M Y') : '-' }}
                                        </td>
                                        <td>{{ $user->department->cname ?? '-' }}</td>

                                        <td>
                                            @if ($user->fhrd)
                                                <span class="badge bg-info text-dark">HRD</span>
                                            @elseif ($user->fsuper)
                                                <span class="badge bg-primary">Supervisor</span>
                                            @elseif ($user->fadmin)
                                                <span class="badge bg-success">Captain</span>
                                            @elseif ($user->fsenior)
                                                <span class="badge bg-warning text-dark">Senior Crew</span>
                                            @else
                                                <span class="badge bg-secondary">Crew</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($user->isActive())
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('backoffice.viewLogs', $user->nid) }}"
                                                class="btn btn-info btn-sm text-white">Lihat</a>
                                            @if (auth()->user()->fhrd == 1)
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLogsModal{{ $user->nid }}">
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('backoffice.viewRequests', $user->nid) }}"
                                                class="btn btn-info btn-sm text-white">Lihat</a>

                                            @if (auth()->user()->fhrd == 1)
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLogsModal{{ $user->nid }}">
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($user->faces->isNotEmpty())
                                                <button class="btn btn-outline-primary btn-sm" title="Lihat Face User"
                                                    onclick="window.location.href='{{ route('hr.face_approval.show', $user->nid) }}'">
                                                    Lihat
                                                </button>
                                            @else
                                                <span class="badge bg-danger">Belum</span>
                                            @endif
                                        </td>

                                        <td>
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->nid }}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>

                                    @include('backoffice.modal.modal_edit_user', [
                                        'user' => $user,
                                        'departments' => $departments ?? collect(),
                                        'rekenings' => $rekenings ?? collect(),
                                    ])

                                    <div class="modal fade" id="deleteLogsModal{{ $user->nid }}" tabindex="-1"
                                        aria-labelledby="deleteLogsModalLabel{{ $user->nid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteLogsModalLabel{{ $user->nid }}">
                                                        Konfimasi Hapus Logs User {{ $user->cname }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Apakah Anda Ingin Menghapus Data Ini?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <form method="POST" action="{{ route('backoffice.deleteLogs') }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user->nid }}">
                                                        <button type="submit" class="btn btn-danger">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="deleteRequestsModal{{ $user->nid }}" tabindex="-1"
                                        aria-labelledby="deleteRequestsModalLabel{{ $user->nid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteRequestsModalLabel{{ $user->nid }}">
                                                        Konfimasi Hapus Requests User {{ $user->cname }} </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Apakah Anda Ingin Menghapus Data Ini?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <form method="POST" action="{{ route('backoffice.deleteRequests') }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user->nid }}">
                                                        <button type="submit" class="btn btn-danger">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center text-muted">
                                            Belum ada data user
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="15" class="text-center text-muted">
                                        Data user tidak tersedia
                                    </td>
                                </tr>
                            @endisset
                        </tbody>


                    </table>
                </div>
            </div>
        </div>

        {{-- disini ya tabel device id --}}
        @include('backoffice.device')

    </div>
    {{-- ====== MODAL KIRIM SLIP GAJI ====== --}}
    @include('backoffice.modal.modal_send_slip')

    {{-- ====== MODAL TAMBAH DEPARTEMEN ====== --}}
    @include('backoffice.modal.modal_tambah_department')

    {{-- ====== MODAL TAMBAH USER ====== --}}
    @include('backoffice.modal.modal_tambah_user')

    {{-- ====== MODAL IMPORT FINGERPRINT ====== --}}
    @include('backoffice.modal.modal_import_fingerprint')

    @include('backoffice.modal.modal_export_user')

    <style>
        th.sortable {
            cursor: pointer;
            user-select: none;
        }

        .sort-icon {
            font-size: 0.8rem;
            margin-left: 4px;
            color: #666;
        }

        th.sortable:hover .sort-icon {
            color: #000;
        }

        #searchInput {
            padding-right: 30px;
        }

        #clearSearch {
            color: #555;
            cursor: pointer;
        }

        #clearSearch:hover {
            color: #000;
        }

        /* === SCROLL & LAYOUT TABEL USER === */
        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-users {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 0;
            /* ⬅️ tidak lagi geser ke kanan */
            min-width: 2000px;
            /* boleh diubah sesuai kebutuhan kolom */
        }

        @media (max-width: 768px) {

            .table-users th,
            .table-users td {
                white-space: nowrap;
                font-size: 11px;
                padding: 4px 6px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== Deklarasi elemen utama =====
            const table = document.getElementById('userTable');
            const tbody = table ? table.querySelector('tbody') : null;
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            // ===== Fungsi Search =====
            if (searchInput && tbody) {
                searchInput.addEventListener('input', function() {
                    const keyword = this.value.toLowerCase();
                    const rows = tbody.querySelectorAll('tr');
                    clearSearch.style.display = keyword ? 'block' : 'none';

                    rows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });

                // Reset Search
                clearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    this.style.display = 'none';
                    tbody.querySelectorAll('tr').forEach(row => row.style.display = '');
                });
            }

            // ===== Fungsi Sorting =====
            if (tbody) {
                let sortState = {
                    column: null,
                    direction: 'asc'
                };

                function sortTable(columnIndex, keyExtractor) {
                    const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.cells.length > 0);
                    const currentDir = sortState.direction;

                    rows.sort((a, b) => {
                        const aText = keyExtractor(a);
                        const bText = keyExtractor(b);
                        return currentDir === 'asc' ?
                            aText.localeCompare(bText) :
                            bText.localeCompare(aText);
                    });

                    tbody.innerHTML = '';
                    rows.forEach(r => tbody.appendChild(r));
                    sortState.direction = currentDir === 'asc' ? 'desc' : 'asc';
                }

                document.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', () => {
                        const column = header.dataset.column;
                        document.querySelectorAll('.sort-icon').forEach(icon => icon.textContent =
                            '↕');

                        if (column === 'cabang') {
                            sortTable(5, row => row.cells[5].innerText.trim());
                            document.getElementById('sortIconCabang').textContent =
                                sortState.direction === 'asc' ? '▲' : '▼';
                        }

                        if (column === 'role') {
                            sortTable(6, row => row.cells[6].innerText.trim());
                            document.getElementById('sortIconRole').textContent =
                                sortState.direction === 'asc' ? '▲' : '▼';
                        }
                    });
                });
            }
        });
    </script>

    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 12000;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function showToast(message, variant = 'primary') {
                const container = document.getElementById('toastContainer');
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${variant} border-0 mb-2`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body fw-semibold">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
                container.appendChild(toastEl);
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 6000
                });
                toast.show();
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            }

            @if (session('slip_status'))
                showToast("{{ addslashes(session('slip_status')) }}", 'success');
            @endif
            @if (session('slip_error'))
                showToast("{{ addslashes(session('slip_error')) }}", 'danger');
            @endif
        });
    </script>
@endsection
