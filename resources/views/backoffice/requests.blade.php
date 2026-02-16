@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
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

        .badge {
            font-size: 0.85rem;
        }
    </style>

    <div class="container py-4">
        <h3 class="text-center mb-4">REQUEST IZIN - {{ $user->cname }}</h3>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('export-request', [
                'user_id' => $user->nid,
                'start_date' => request('start_date', now()->subMonth()->format('Y-m-d')),
                'end_date' => request('end_date', now()->format('Y-m-d')),
            ]) }}"
                class="btn btn-success fw-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel - {{ $user->cname }}
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center align-top">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Lokasi</th>
                        <th>Status Approval</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        <tr class="text-center align-top">
                            <td>{{ $req->nid }}</td>
                            <td>{{ \Carbon\Carbon::parse($req->drequest)->format('d/m/Y') }}</td>
                            <td>{{ $req->creason }}</td>

                            {{-- Lokasi --}}
                            <td>
                                @if (!empty($req->cplacename))
                                    {{ $req->cplacename }}
                                @else
                                    {{ $req->nlat }}, {{ $req->nlng }}
                                @endif
                            </td>

                            {{-- 🔹 Status Approval --}}
                            {{-- 🔹 Status Approval --}}
                            <td>
                                @php
                                    $isCaptain =
                                        auth()->user()->fadmin == 1 &&
                                        auth()->user()->fsuper == 0 &&
                                        auth()->user()->fhrd == 0;
                                    $isHrd = auth()->user()->fhrd == 1;

                                    // Tentukan status akhir gabungan
                                    $finalStatus = 'pending';
                                    $finalBy = '';

                                    if ($req->fadmreq == 1) {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Admin';
                                    } elseif ($req->cstatus === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'Captain';
                                    } elseif ($req->chrdstat === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'HRD';
                                    } elseif ($req->cstatus === 'approved' && $req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain & HRD';
                                    } elseif ($req->cstatus === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain';
                                    } elseif ($req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'HRD';
                                    }
                                @endphp

                                {{-- ✅ Jika dibuat admin langsung auto-approved --}}
                                @if ($req->fadmreq == 1)
                                    <span class="badge bg-success">Approved (By Admin)</span>

                                    {{-- ✅ Jika sudah ada hasil final --}}
                                @elseif ($finalStatus !== 'pending')
                                    <span class="badge {{ $finalStatus === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($finalStatus) }} by {{ $finalBy }}
                                    </span>

                                    {{-- ✅ Jika masih pending, tampilkan tombol sesuai role --}}
                                @else
                                    @if ($isCaptain)
                                        <form action="{{ route('mrequest.approve.captain', $req->nid) }}" method="POST"
                                            class="approve-form d-inline">
                                            @csrf
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @elseif ($isHrd)
                                        <form action="{{ route('mrequest.approve.hrd', $req->nid) }}" method="POST"
                                            class="approve-form d-inline">
                                            @csrf
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                @endif
                            </td>


                            {{-- Foto --}}
                            <td>
                                @if (!empty($req->cphoto_path))
                                    @php
                                        $photoUrl = 'https://absensi.matahati.my.id/' . ltrim($req->cphoto_path, '/');
                                    @endphp
                                    <a href="{{ $photoUrl }}" target="_blank">
                                        <img src="{{ $photoUrl }}" alt="Foto Request"
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
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $requests->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- 🧠 Script Approval SweetAlert --}}
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

                            // Ganti tombol dengan badge status
                            statusCell.innerHTML = `
                                <span class="badge ${clickedButton.value === 'approved' ? 'bg-success' : 'bg-danger'}">
                                    ${clickedButton.value === 'approved' ? 'Approved' : 'Rejected'}
                                </span>
                            `;
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
@endsection
