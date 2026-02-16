@extends('layouts.app')

@section('title', 'Halaman Penggajian')

@section('content')

    @if (auth()->user()->fhrd == 1)
        <link rel="stylesheet" href="{{ asset('css/penggajian.css') }}?v=1">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-gaji btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahRekening">
                + Tambah Rekening
            </button>
        </div>

        @include('penggajian.components.table_rekening')

        @include('penggajian.modals.modal_tambah_rekening')

        @include('penggajian.modals.modal_edit_rekening')

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-gaji btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahGaji">
                + Tambah Gaji
            </button>
        </div>

        @include('penggajian.components.table_tunjangan')

        @include('penggajian.modals.modal_tambah_gaji')

        @php
            // get current department query parameter (validated in controller ideally)
            $curDepRaw = request()->query('department_id', '');

            // build list of valid department ids as strings
            $validDepIds = $departments
                ->pluck('id')
                ->map(function ($v) {
                    return (string) $v;
                })
                ->toArray();

            // if query param not in valid list, treat as empty (All)
            $curDep = in_array((string) $curDepRaw, $validDepIds, true) ? (string) $curDepRaw : '';
        @endphp

        <div class="d-flex justify-content-between align-items-start mb-3">
            {{-- LEFT: filter departemen --}}
            <form id="formDepartmentFilter" method="GET" action="{{ route('penggajian.index') }}"
                class="d-flex align-items-center gap-2" autocomplete="off">
                <input type="hidden" name="year" value="{{ $selYear }}">
                <input type="hidden" name="month" value="{{ $selMonth }}">

                <select id="departmentFilter" name="department_id" class="form-select" style="width:220px;"
                    autocomplete="off">
                    <option value="" {{ $curDep === '' ? 'selected' : '' }}>Semua Departemen</option>
                    @foreach ($departments as $dep)
                        <option value="{{ $dep->nid }}" {{ (string) $curDep === (string) $dep->nid ? 'selected' : '' }}>
                            {{ $dep->cname }}
                        </option>
                    @endforeach
                </select>


                @if ($curDep !== '')
                    <a href="{{ route('penggajian.index', ['year' => $selYear, 'month' => $selMonth]) }}"
                        class="btn btn-secondary btn-sm" id="btnResetDepartment">Reset</a>
                @endif
            </form>

            {{-- RIGHT: tombol aksi --}}
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-gaji btn-success" data-bs-toggle="modal" data-bs-target="#modalPayrollBank">
                    Export Payroll Bank
                </button>

                <button class="btn btn-gaji btn-success" data-bs-toggle="modal" data-bs-target="#modalReportGaji">
                    Export Report Gaji
                </button>

                <form id="formExportExcel" action="{{ route('gaji.export') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="month" value="{{ $selMonth }}">
                    <input type="hidden" name="year" value="{{ $selYear }}">
                    <input type="hidden" name="selected_ids" id="selected_ids">
                    <input type="hidden" name="source_table" id="source_table" value="{{ $exportSource ?? 'csalary' }}">
                    {{-- PASANG department_id supaya export mengikuti filter --}}
                    <input type="hidden" name="department_id" id="export_department_id"
                        value="{{ request('department_id') ?? '' }}">
                </form>

                <button class="btn btn-gaji btn-warning" data-bs-toggle="modal" data-bs-target="#modalKirimSlip">
                    Kirim SLip Gaji
                </button>

                <button class="btn btn-gaji btn-primary" data-bs-toggle="modal" data-bs-target="#modalHitungGaji">
                    Hitung Gaji
                </button>
            </div>
        </div>

        @include('penggajian.components.table_payroll')

        @include('penggajian.modals.modal_hitung')

        @include('penggajian.modals.modal_kirim_slip')

        @include('penggajian.modals.modal_payroll_bank')

        @include('penggajian.modals.modal_edit')

        @include('penggajian.modals.modal_report_gaji')

        <script src="{{ asset('js/penggajian.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deptSelect = document.getElementById('departmentFilter');
                const tableBody = document.getElementById('tablePayrollBody');
                const exportDeptInput = document.getElementById('export_department_id');
                const selYear = "{{ $selYear }}";
                const selMonth = "{{ $selMonth }}";

                if (!deptSelect) {
                    console.error('departmentFilter element not found!');
                    return;
                }
                if (!tableBody) {
                    console.error('tablePayrollBody element not found!');
                    return;
                }

                // helper: parse rows from HTML (used only if server returns HTML as fallback)
                function extractRowsDataFromHtml(htmlText) {
                    const tmp = document.createElement('tbody');
                    tmp.innerHTML = htmlText;
                    const result = [];
                    const trs = Array.from(tmp.querySelectorAll('tr'));
                    trs.forEach(tr => {
                        const dept = (tr.getAttribute('data-department-id') || '').toString().trim();
                        const chk = tr.querySelector('input.payroll-row-checkbox');
                        if (chk) {
                            const uid = (chk.value || '').toString().trim();
                            const name = (chk.getAttribute('data-name') || chk.dataset.name || '').toString()
                                .trim();
                            result.push({
                                user_id: uid,
                                name: name,
                                department_id: dept
                            });
                            return;
                        }
                        const uid = (tr.getAttribute('data-user-id') || '').toString().trim();
                        const tds = tr.querySelectorAll('td');
                        const nameFromTd = (tds[2] && tds[2].textContent || '').toString().trim();
                        result.push({
                            user_id: uid,
                            name: nameFromTd,
                            department_id: dept
                        });
                    });
                    return result;
                }

                // group rows by department
                function groupByDepartment(rows) {
                    const map = {};
                    rows.forEach(r => {
                        const did = (r.department_id || '').toString().trim() || '(no-dept)';
                        const name = (r.name || '').toString().trim() || '(no-name)';
                        if (!map[did]) map[did] = [];
                        if (!map[did].includes(name)) map[did].push(name);
                    });
                    return map;
                }

                // log groups in required format
                function consoleLogDepartmentGroups(map) {
                    const keys = Object.keys(map);
                    if (keys.length === 0) {
                        console.log('[no rows returned]');
                        return;
                    }
                    keys.forEach(k => {
                        const names = map[k].join(', ');
                        console.log(`[${k} = ${names}]`);
                    });
                }

                // main handler
                deptSelect.addEventListener('change', function() {
                    const department_id = this.value;
                    console.log('Selected department_id ->', department_id);

                    const url = "{{ route('penggajian.filter.department') }}" +
                        "?department_id=" + encodeURIComponent(department_id) +
                        "&year=" + encodeURIComponent(selYear) +
                        "&month=" + encodeURIComponent(selMonth);

                    tableBody.innerHTML = '<tr><td colspan="999">Loading...</td></tr>';

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
                            return res.json();
                        })
                        .then(json => {
                            // json expected: { success, html, count, users_by_dept }
                            const html = json.html || '';
                            tableBody.innerHTML = html ||
                                '<tr><td colspan="999" class="text-muted">Data belum tersedia</td></tr>';

                            // use users_by_dept if provided for logging; fallback: parse html
                            const usersByDept = json.users_by_dept || null;
                            if (usersByDept) {
                                Object.keys(usersByDept).forEach(did => {
                                    console.log(`[${did} = ${usersByDept[did].join(', ')}]`);
                                    console.log(`count for [${did}] = ${usersByDept[did].length}`);
                                });
                            } else {
                                // fallback parse
                                const rowsData = extractRowsDataFromHtml(html);
                                const group = groupByDepartment(rowsData);
                                consoleLogDepartmentGroups(group);
                                Object.keys(group).forEach(did => {
                                    console.log(`count for [${did}] = ${group[did].length}`);
                                });
                            }

                            // update hidden export field
                            if (exportDeptInput) exportDeptInput.value = department_id ?? '';

                            // defensive client-side hide: show only selected department rows
                            if (department_id && department_id.toString().trim() !== '') {
                                const rows = Array.from(tableBody.querySelectorAll(
                                    'tr[data-department-id]'));
                                rows.forEach(r => {
                                    const rid = (r.getAttribute('data-department-id') || '')
                                        .toString();
                                    r.style.display = rid === String(department_id) ? '' : 'none';
                                });
                            } else {
                                tableBody.querySelectorAll('tr[data-department-id]').forEach(r => r.style
                                    .display = '');
                            }

                            // log visible departments after apply
                            const visibleIds = Array.from(tableBody.querySelectorAll(
                                    'tr[data-department-id]'))
                                .map(r => r.getAttribute('data-department-id'))
                                .filter(Boolean);
                            console.log('Visible department_ids after apply:', Array.from(new Set(
                                visibleIds)));

                            // update URL (so page is bookmarkable)
                            try {
                                const newUrl = new URL(window.location.href);
                                if (department_id) newUrl.searchParams.set('department_id', department_id);
                                else newUrl.searchParams.delete('department_id');
                                newUrl.searchParams.set('year', selYear);
                                newUrl.searchParams.set('month', selMonth);
                                window.history.replaceState({}, '', newUrl.toString());
                            } catch (e) {
                                /* ignore */
                            }

                            // re-init handlers (if any)
                            if (window.initPayrollRowHandlers) window.initPayrollRowHandlers();
                        })
                        .catch(err => {
                            console.error('Filter error', err);
                            // as fallback, try to fetch as text and inject
                            tableBody.innerHTML =
                                '<tr><td colspan="999">Gagal memuat data. Cek console.</td></tr>';
                        });
                });

                // trigger filtering on page load if dept param exists (auto apply)
                const initialDep = deptSelect.value;
                if (initialDep && initialDep.toString().trim() !== '') {
                    // short delay to allow page to finish rendering
                    setTimeout(() => deptSelect.dispatchEvent(new Event('change', {
                        bubbles: true
                    })), 80);
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bankSelect = document.getElementById('bankSelect');
                const rekeningBox = document.getElementById('rekeningMandiriBox');
                const mrekeningSelect = document.getElementById('mrekeningSelect');

                // safety: jika elemen tidak ada, jangan throw
                function toggleMandiriOptions() {
                    if (!bankSelect) return;
                    const isMandiri = String(bankSelect.value || '').toLowerCase() === 'mandiri';
                    if (rekeningBox) rekeningBox.style.display = isMandiri ? 'block' : 'none';
                    if (mrekeningSelect) {
                        Array.from(mrekeningSelect.options).forEach(o => {
                            const b = (o.dataset.bank || '').toLowerCase();
                            o.style.display = (!isMandiri || b.includes('mandiri')) ? '' : 'none';
                        });
                        if (!isMandiri) mrekeningSelect.value = '';
                    }
                }

                // init toggle on load
                toggleMandiriOptions();

                // listen change
                if (bankSelect) bankSelect.addEventListener('change', toggleMandiriOptions);

                // Optional: simple UX - disable submit while validating (not required)
                const form = document.getElementById('formExportBank');
                if (form) {
                    form.addEventListener('submit', function() {
                        // basic client-side check: bank wajib
                        const bank = (bankSelect && bankSelect.value) ? bankSelect.value : '';
                        if (!bank) {
                            alert('Pilih format bank terlebih dahulu.');
                            event.preventDefault();
                            return false;
                        }
                        // If Mandiri selected, ensure a company account selected
                        if (String(bank).toLowerCase() === 'mandiri' && mrekeningSelect && !mrekeningSelect
                            .value) {
                            alert('Silakan pilih Rekening Sumber untuk Mandiri.');
                            event.preventDefault();
                            return false;
                        }
                        // otherwise allow submit (GET) — department_id akan dikirim otomatis dari select
                        return true;
                    });
                }
            });
        </script>
    @else
        <div class="container mt-5">
            <div class="alert alert-warning text-center shadow-sm">
                <h5 class="mb-2">Menu Tidak Tersedia</h5>
            </div>
        </div>
    @endif

@endsection
