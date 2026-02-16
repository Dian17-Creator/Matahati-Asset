@extends('layouts.app')

@section('content')

    <div class="container mt-4">
        @if ($authUser->fsuper == 1 || $authUser->fhrd == 1)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Manajemen Jadwal & Shift</h2>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    + Tambah Shift Baru
                </button>
            </div>
        @endif

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($authUser->fsuper == 1 || $authUser->fhrd == 1)
            {{-- Tabel daftar shift --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Daftar Shift</div>
                <div class="card-body">
                    <table class="table table-bordered align-middle text-center">
                        <thead>
                            <tr>
                                <th>Nama Shift</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Mulai (Split)</th>
                                <th>Selesai (Split)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($masters as $shift)
                                <tr>
                                    <td>{{ $shift->cname }}</td>
                                    <td>{{ substr($shift->dstart, 0, 5) }}</td>
                                    <td>{{ substr($shift->dend, 0, 5) }}</td>
                                    <td>
                                        {{ $shift->dstart2 ? substr($shift->dstart2, 0, 5) : '-' }}
                                    </td>
                                    <td>
                                        {{ $shift->dend2 ? substr($shift->dend2, 0, 5) : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editShiftModal{{ $shift->nid }}">
                                                Edit
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ url('/schedule/' . $shift->nid) }}" method="POST"
                                                onsubmit="return confirm('Hapus shift ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>



                                {{-- Modal Edit Shift --}}
                                <div class="modal fade" id="editShiftModal{{ $shift->nid }}" tabindex="-1"
                                    aria-labelledby="editShiftModalLabel{{ $shift->nid }}" aria-hidden="true">

                                    @if ($errors->has('split') && session('edit_shift_id') == $shift->nid)
                                        <div class="alert alert-danger">
                                            {{ $errors->first('split') }}
                                        </div>
                                    @endif

                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ url('/schedule/' . $shift->nid) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary text-white">
                                                    <h5 class="modal-title" id="editShiftModalLabel{{ $shift->nid }}">
                                                        Edit
                                                        Shift</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Nama Shift</label>
                                                        <input type="text" name="cname" class="form-control"
                                                            value="{{ $shift->cname }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Mulai</label>
                                                            <input type="time" name="dstart" class="form-control"
                                                                value="{{ substr($shift->dstart, 0, 5) }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Selesai</label>
                                                            <input type="time" name="dend" class="form-control"
                                                                value="{{ substr($shift->dend, 0, 5) }}" required>
                                                        </div>
                                                        <div class="d-flex align-items-center my-3">
                                                            <div class="flex-grow-1 border-top border-secondary"></div>
                                                            <span class="mx-3 fw-semibold text-secondary">SPLIT</span>
                                                            <div class="flex-grow-1 border-top border-secondary"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Mulai</label>
                                                            <input type="time" name="dstart2" class="form-control"
                                                                value="{{ substr($shift->dstart2, 0, 5) }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Selesai</label>
                                                            <input type="time" name="dend2" class="form-control"
                                                                value="{{ substr($shift->dend2, 0, 5) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success text-white">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">Belum ada data shift</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Form assign jadwal ke user --}}
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>Atur Shift Karyawan</span>
                <!-- Tombol Import jadi buka modal -->
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importScheduleModal">
                    + Import Jadwal User
                </button>
            </div>
            <div class="card-body">
                <form action="{{ route('schedule.generate') }}" method="POST" id="scheduleForm">
                    @csrf
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-4">
                            <label>Pilih Karyawan</label>
                            <select name="nuserid" class="form-select" required>
                                <option value="">-- pilih karyawan --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->nid }}">{{ $user->cname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label>Filter Kalender</label>
                            <select id="filterPeriode" class="form-select">
                                <option value="">-- Pilih Periode --</option>
                                <option value="this_week">Minggu Ini</option>
                                <option value="last_week">Minggu Lalu</option>
                                <option value="next_week">Minggu Depan</option>
                                <option value="this_month">Bulan Ini</option>
                                <option value="next_month">Bulan Depan</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Atur</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Kontrak Kerja Karyawan --}}
        @if (auth()->user()->fhrd == 1)
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Daftar Kontrak Kerja Karyawan</span>
                    @if (auth()->user()->fhrd == 1)
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addContractModal">
                            + Tambah Kontrak
                        </button>
                    @endif
                </div>

                <div class="card-body">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Akhir</th>
                                <th>Tipe Kontrak</th>
                                <th>Status</th>
                                <th>Sisa Hari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contracts as $contract)
                                <tr>
                                    {{-- Nama Pegawai --}}
                                    <td>{{ $contract->user->cname ?? '-' }}</td>

                                    {{-- Tanggal Mulai --}}
                                    <td>
                                        {{ $contract->dstart ? \Carbon\Carbon::parse($contract->dstart)->format('d/m/Y') : '-' }}
                                    </td>

                                    {{-- Tanggal Akhir --}}
                                    <td>
                                        {{ $contract->dend ? \Carbon\Carbon::parse($contract->dend)->format('d/m/Y') : '-' }}
                                    </td>

                                    {{-- Tipe Kontrak --}}
                                    <td>
                                        @if ($contract->ctermtype === 'probation')
                                            <span class="badge bg-warning text-dark">Probation</span>
                                        @elseif ($contract->ctermtype === 'promotion')
                                            <span class="badge bg-info text-dark">Promotion</span>
                                        @else
                                            <span class="badge bg-secondary">Evaluation</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if ($contract->cstatus === 'active')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Dihentikan</span>
                                        @endif
                                    </td>

                                    {{-- Sisa Hari --}}
                                    <td>
                                        @php
                                            // pastikan remaining_days ada; kalau tidak, hitung dari dend
                                            if (isset($contract->remaining_days)) {
                                                $remaining = floor($contract->remaining_days);
                                            } else {
                                                $remaining = null;
                                                if ($contract->dend) {
                                                    $remaining = \Carbon\Carbon::parse($contract->dend)->diffInDays(
                                                        \Carbon\Carbon::now(),
                                                        false,
                                                    );
                                                }
                                            }
                                        @endphp

                                        @if (!is_null($remaining) && $remaining > 0)
                                            <span class="text-success fw-bold">{{ $remaining }} hari lagi</span>
                                        @elseif ($remaining === 0)
                                            <span class="text-warning fw-bold">Habis hari ini</span>
                                        @else
                                            <span class="text-danger fw-bold">Sudah Habis</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td>
                                        @if (auth()->user()->fhrd == 1)
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editContractModal{{ $contract->nid }}">
                                                Edit
                                            </button>

                                            <form action="{{ route('schedule.contract.destroy', $contract->nid) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus kontrak ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Edit Kontrak --}}
                                <div class="modal fade" id="editContractModal{{ $contract->nid }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('schedule.contract.update', $contract->nid) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">Edit Kontrak</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Pegawai</label>
                                                        <select name="nuserid" class="form-select" required>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->nid }}"
                                                                    {{ $contract->nuserid == $user->nid ? 'selected' : '' }}>
                                                                    {{ $user->cname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tanggal Mulai</label>
                                                            <input type="date" name="dstart" class="form-control"
                                                                value="{{ \Carbon\Carbon::parse($contract->dstart)->format('Y-m-d') }}"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tanggal Akhir</label>
                                                            <input type="date" name="dend" class="form-control"
                                                                value="{{ \Carbon\Carbon::parse($contract->dend)->format('Y-m-d') }}"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Durasi</label>
                                                            <select name="nterm" class="form-select">
                                                                <option value="3"
                                                                    {{ $contract->nterm == 3 ? 'selected' : '' }}>3 bulan
                                                                </option>
                                                                <option value="6"
                                                                    {{ $contract->nterm == 6 ? 'selected' : '' }}>6 bulan
                                                                </option>
                                                                <option value="12"
                                                                    {{ $contract->nterm == 12 ? 'selected' : '' }}>12 bulan
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tipe Kontrak</label>
                                                            <select name="ctermtype" class="form-select">
                                                                <option value="probation"
                                                                    {{ $contract->ctermtype == 'probation' ? 'selected' : '' }}>
                                                                    Probation</option>
                                                                <option value="promotion"
                                                                    {{ $contract->ctermtype == 'promotion' ? 'selected' : '' }}>
                                                                    Promotion</option>
                                                                <option value="evaluation"
                                                                    {{ $contract->ctermtype == 'evaluation' ? 'selected' : '' }}>
                                                                    Evaluation</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Status</label>
                                                        <select name="cstatus" class="form-select">
                                                            <option value="active"
                                                                {{ $contract->cstatus == 'active' ? 'selected' : '' }}>
                                                                Aktif
                                                            </option>
                                                            <option value="terminated"
                                                                {{ $contract->cstatus == 'terminated' ? 'selected' : '' }}>
                                                                Dihentikan</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success text-white">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted">Belum ada data kontrak kerja</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Import Jadwal --}}

    <div class="modal fade" id="importScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form id="importScheduleForm" action="{{ route('file-import-schedule') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-upload me-2"></i>Import Jadwal Kerja
                        </h5>
                    </div>

                    <div class="modal-body">
                        {{-- Notifikasi --}}
                        @if ($errors->has('file'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ $errors->first('file') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($sukses = Session::get('userschedulesuccess'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <strong>{{ $sukses }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($warnings = Session::get('userschedule_warning'))
                            <div class="alert alert-warning alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach ($warnings as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="text-center">
                            <h5 class="mb-3">Pilih File Jadwal (format .xlsx)</h5>
                            <div class="form-group" style="max-width: 500px; margin: 0 auto;">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile"
                                        accept=".xlsx" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- Modal Tambah Shift --}}
    <div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ url('/schedule') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="addShiftModalLabel">Tambah Shift Baru</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Shift</label>
                            <input type="text" name="cname" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jam Mulai</label>
                                <input type="time" name="dstart" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Selesai</label>
                                <input type="time" name="dend" class="form-control" required>
                            </div>
                            <div class="d-flex align-items-center my-3">
                                <div class="flex-grow-1 border-top border-secondary"></div>
                                <span class="mx-3 fw-semibold text-secondary">SPLIT</span>
                                <div class="flex-grow-1 border-top border-secondary"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Mulai</label>
                                <input type="time" name="dstart2" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Selesai</label>
                                <input type="time" name="dend2" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 🟢 Modal Tambah Kontrak -->
    <div class="modal fade" id="addContractModal" tabindex="-1" aria-labelledby="addContractModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('schedule.contract.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="addContractModalLabel">Tambah Kontrak Baru</h5>
                    </div>

                    <div class="modal-body">

                        {{-- Pegawai --}}
                        <div class="mb-3">
                            <label>Pegawai</label>
                            <select name="nuserid" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->nid }}">{{ $user->cname }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Mulai & Akhir --}}
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Mulai</label>
                                <input type="date" id="contract_start" name="dstart" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Akhir</label>
                                <input type="date" id="contract_end" name="dend" class="form-control" required>
                            </div>
                        </div>

                        {{-- Durasi & Tipe Kontrak --}}
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label>Durasi Kontrak</label>
                                <select id="contract_duration" name="nterm" class="form-select" required>
                                    <option value="">-- Pilih Durasi --</option>
                                    <option value="3">3 Bulan</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="12">12 Bulan</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tipe Kontrak</label>
                                <select name="ctermtype" class="form-select" required>
                                    <option value="probation">Probation</option>
                                    <option value="promotion">Promotion</option>
                                    <option value="evaluation">Evaluation</option>
                                </select>
                            </div>
                        </div>

                    </div> {{-- end modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/schedule.js') }}?v={{ time() }}"></script>

        @if (session('edit_shift_id'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const modalId = "editShiftModal{{ session('edit_shift_id') }}";
                    const modalEl = document.getElementById(modalId);
                    if (modalEl) {
                        new bootstrap.Modal(modalEl).show();
                    }
                });
            </script>
        @endif
    @endpush
@endsection
