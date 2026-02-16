@extends('layouts.app')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" /> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* [id^="map_"] {
                                                height: 200px;
                                                width: 200px;
                                                border-radius: 10px;
                                            } */

        th a {
            color: inherit;
            text-decoration: none;
            transition: 0.2s;
        }

        th a:hover {
            color: #FF6F51;
        }

        .active-sort {
            color: #FF6F51;
        }

        td>div[id^="map_"] {
            margin: 0 auto;
            display: block;
        }
    </style>

    <div class="container py-4">
        <h3 class="text-center">LOG ABSENSI - {{ $user->cname }}</h3>

        {{-- Filter tanggal --}}
        <form method="GET" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                </div>

                <div class="col-md-4 d-flex justify-content-end">
                    <a href="{{ route('export-mscan', [
                        'user_id' => $user->nid,
                        'start_date' => request('start_date', now()->subMonth()->format('Y-m-d')),
                        'end_date' => request('end_date', now()->format('Y-m-d')),
                    ]) }}"
                        class="btn btn-success fw-bold">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel - {{ $user->cname }}
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabel log --}}
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Alasan</th>
                    <th>Tipe Absen</th>
                    <th>Status Approval</th>
                    {{-- <th>Peta</th> --}}
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="text-center align-top">
                        <td>{{ $log->nid }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->dscanned)->format('d/m/Y H:i:s') }}</td>

                        {{-- Lokasi --}}
                        <td>
                            @if (!empty($log->cplacename))
                                {{ $log->cplacename }}
                            @elseif (!empty($log->nlat) && !empty($log->nlng))
                                {{ $log->nlat }}, {{ $log->nlng }}
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>

                        <td>{{ $log->creason ?? '-' }}</td>

                        {{-- 🔸 Jenis absen --}}
                        <td>
                            @if ($log->source === 'face')
                                <span class="badge bg-info text-white">Face</span>
                            @elseif ($log->source === 'forgot')
                                <span class="badge bg-danger text-white">Lupa Absen</span>
                            @elseif ($log->source === 'manual' || (!empty($log->fmanual) && $log->fmanual == 1))
                                <span class="badge bg-warning text-dark">Manual</span>
                            @else
                                <span class="badge bg-primary">Scan</span>
                            @endif
                        </td>

                        {{-- 🔹 Status Approval --}}
                        <td>
                            {{-- ================= FACE ================= --}}
                            @if ($log->source === 'face')
                                <span class="badge bg-success">Accepted</span>

                                {{-- ================= FORGOT (HRD ONLY) ================= --}}
                            @elseif ($log->source === 'forgot')
                                @if ($log->cstatus === 'approved')
                                    <span class="badge bg-success">Approved by HRD</span>
                                @elseif ($log->cstatus === 'rejected')
                                    <span class="badge bg-danger">Rejected by HRD</span>
                                @else
                                    @if (auth()->user()->fhrd == 1)
                                        <form action="{{ route('forgot.approve', $log->nid) }}" method="POST"
                                            class="approve-form d-inline">
                                            @csrf
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Waiting HRD</span>
                                    @endif
                                @endif

                                {{-- ================= MANUAL ================= --}}
                            @elseif ($log->source === 'manual' || (!empty($log->fmanual) && $log->fmanual == 1))
                                @php
                                    $isCaptain =
                                        auth()->user()->fadmin == 1 &&
                                        auth()->user()->fsuper == 0 &&
                                        auth()->user()->fhrd == 0;
                                    $isHrd = auth()->user()->fhrd == 1;

                                    $hasBeenProcessed =
                                        (isset($log->cstatus) && $log->cstatus !== 'pending') ||
                                        (isset($log->chrdstat) && $log->chrdstat !== 'pending');
                                @endphp

                                @if ($hasBeenProcessed)
                                    <span
                                        class="badge
                                        @if ($log->cstatus === 'approved' || $log->chrdstat === 'approved') bg-success
                                        @elseif ($log->cstatus === 'rejected' || $log->chrdstat === 'rejected') bg-danger
                                        @else bg-secondary @endif">
                                        {{ $log->cstatus === 'approved' ? 'Approved by Captain' : '' }}
                                        {{ $log->cstatus === 'rejected' ? 'Rejected by Captain' : '' }}
                                        {{ $log->chrdstat === 'approved' ? 'Approved by HRD' : '' }}
                                        {{ $log->chrdstat === 'rejected' ? 'Rejected by HRD' : '' }}
                                    </span>
                                @else
                                    @if ($isCaptain)
                                        <form action="{{ route('mscan.approve.captain', $log->nid) }}" method="POST"
                                            class="approve-form d-inline">
                                            @csrf
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @elseif ($isHrd)
                                        <form action="{{ route('mscan.approve.hrd', $log->nid) }}" method="POST"
                                            class="approve-form d-inline">
                                            @csrf
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @else
                                        <small class="text-muted">Pending</small>
                                    @endif
                                @endif

                                {{-- ================= SCAN BIASA ================= --}}
                            @else
                                <span class="badge bg-success">Accepted</span>
                            @endif
                        </td>

                        {{-- 🗺️ Peta --}}
                        {{-- <td>
                            <div id="map_{{ $log->nid }}"></div>
                        </td> --}}

                        {{-- 📸 Foto --}}
                        <td>
                            @if (!empty($log->cphoto_path))
                                @php
                                    $photoUrl = 'https://absensi.matahati.my.id/' . ltrim($log->cphoto_path, '/');
                                @endphp
                                <a href="{{ $photoUrl }}" target="_blank">
                                    <img src="{{ $photoUrl }}" alt="Foto Absen"
                                        style="width:150px;height:150px;object-fit:cover;border-radius:6px;"
                                        onerror="this.onerror=null; this.replaceWith(document.createTextNode('-'));" />
                                </a>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- 🔹 SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll("form.approve-form");

            forms.forEach(form => {
                form.addEventListener("submit", async function(e) {
                    e.preventDefault();

                    const clickedButton = e.submitter;
                    const formData = new FormData(this);
                    formData.append("status", clickedButton.value);

                    const url = this.action;
                    const statusCell = this.closest('td');
                    const buttons = this.querySelectorAll("button");

                    buttons.forEach(btn => {
                        btn.disabled = true;
                        btn.innerText = "Processing...";
                    });

                    try {
                        const response = await fetch(url, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // ubah status langsung
                            statusCell.innerHTML = `
                                <span class="badge ${clickedButton.value === 'approved' ? 'bg-success' : 'bg-danger'}">
                                    ${clickedButton.value === 'approved' ? 'Approved' : 'Rejected'}
                                </span>`;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message ||
                                    'Terjadi kesalahan saat memperbarui status.'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memproses permintaan.'
                        });
                    } finally {
                        buttons.forEach(btn => btn.disabled = false);
                    }
                });
            });
        });
    </script>

    {{-- 🗺️ Leaflet Map --}}
    {{-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($logs as $log)
                (function() {
                    const mapId = 'map_{{ $log->nid }}';
                    const div = document.getElementById(mapId);
                    if (!div) return;

                    const lat = parseFloat('{{ $log->nlat ?? 0 }}');
                    const lng = parseFloat('{{ $log->nlng ?? 0 }}');
                    if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) {
                        div.innerHTML = '<small class="text-muted">Lokasi tidak tersedia</small>';
                        return;
                    }

                    setTimeout(() => {
                        const map = L.map(mapId).setView([lat, lng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19
                        }).addTo(map);
                        L.marker([lat, lng]).addTo(map).bindPopup('📍 Lokasi Absen');
                        map.invalidateSize();
                    }, 250);
                })();
            @endforeach
        });
    </script> --}}
@endsection
