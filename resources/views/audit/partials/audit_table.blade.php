<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">
        <b>Master Audit</b>
        <span class="badge bg-light text-dark">{{ $data->total() }} data</span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Kode Asset</th>
                        <th>Nama Asset</th>
                        <th>Status</th>
                        <th>Qty Sistem</th>
                        <th>Qty Real</th>
                        <th>Catatan</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $row)
                        <tr>
                            <td class="text-center">{{ $data->firstItem() + $i }}</td>

                            <td>{{ $row->department->cname ?? '-' }}</td>

                            <td class="text-center">
                                {{ $row->dtrans ? $row->dtrans->format('d-m-Y') : '-' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-primary">{{ $row->ckode }}</span>
                            </td>

                            <td>{{ $row->cnama ?? '-' }}</td>

                            <td class="text-center">
                                @if ($row->cstatus === 'BAIK/SESUAI')
                                    <span class="badge bg-success">{{ $row->cstatus }}</span>
                                @elseif($row->cstatus === 'MASALAH/TDK.SESUAI')
                                    <span class="badge bg-danger">{{ $row->cstatus }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $row->cstatus ?? '-' }}</span>
                                @endif
                            </td>

                            <td class="text-center">{{ $row->nqty ?? 0 }}</td>

                            <td class="text-center">{{ $row->nqtyreal ?? 0 }}</td>

                            <td>{{ $row->ccatatan ?? '-' }}</td>

                            <td class="text-center">
                                @if ($row->dreffoto)
                                    <a href="{{ asset('assets/audit/' . basename($row->dreffoto)) }}" target="_blank">
                                        <img src="{{ asset('assets/audit/' . basename($row->dreffoto)) }}"
                                            style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                Belum ada data audit
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-center mt-2">
            {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
