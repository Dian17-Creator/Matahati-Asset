{{-- resources/views/penggajian/components/table_payroll_rows.blade.php --}}
@php
    if (empty($data) || !is_array($data) || count($data) === 0) {
        echo '<tr><td colspan="999" class="text-muted">Data belum tersedia</td></tr>';
        return;
    }
@endphp

@foreach ($data as $row)
    @php
        $r = is_object($row) ? (array) $row : (array) $row;
        $userId = $r['user_id'] ?? '';
        $ketDisplay = $r['keterangan_absensi'] ?? 'A = 0, I = 0, S = 0';
    @endphp

    <tr data-user-id="{{ e($userId) }}" data-department-id="{{ e($r['department_id'] ?? '') }}">
        <td>{{ $loop->iteration }}</td>
        <td>
            <input type="checkbox" class="payroll-row-checkbox" value="{{ e($r['id'] ?? '') }}"
                data-name="{{ e($r['user_name'] ?? '') }}" data-jabatan="{{ e($r['jabatan'] ?? '') }}"
                data-hari="{{ e($r['jumlah_masuk'] ?? 0) }}">
        </td>
        <td>{{ e($r['user_name'] ?? '-') }}</td>
        <td>{{ e($r['jabatan'] ?? '-') }}</td>
        <td class="text-center">{{ e($r['jumlah_masuk'] ?? 0) }}</td>
        <td class="text-end">{{ e($r['gaji'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['gaji_pokok'] ?? 'Rp 0') }}</td>

        <td class="text-end">{{ e($r['tunjangan_makan'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['tunjangan_jabatan'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['tunjangan_transport'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['tunjangan_luar_kota'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['tunjangan_masa_kerja'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['gaji_lembur'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['tabungan_diambil'] ?? 'Rp 0') }}</td>

        <td class="text-end">{{ e($r['potongan_lain'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['potongan_tabungan'] ?? 'Rp 0') }}</td>
        <td class="text-end">{{ e($r['potongan_keterlambatan'] ?? 'Rp 0') }}</td>

        <td class="text-end">{{ e($r['total_gaji'] ?? 'Rp 0') }}</td>
        <td>{{ e($r['note'] ?? '-') }}</td>

        <td class="ket-absensi-cell">{{ e($ketDisplay) }}</td>

        <td>{{ e($r['reasonedit'] ?? '-') }}</td>

        <td>
            <button class="btn btn-sm btn-warning btn-open-edit" data-id="{{ e($r['id'] ?? '') }}"
                data-user-id="{{ e($r['user_id'] ?? '') }}" data-jumlah-masuk="{{ e($r['jumlah_masuk'] ?? 0) }}"
                data-gaji-harian="{{ e($r['gaji_harian'] ?? 0) }}" data-gaji-pokok="{{ e($r['gaji_pokok'] ?? 0) }}"
                data-tunjangan-makan="{{ e($r['tunjangan_makan'] ?? 0) }}"
                data-tunjangan-jabatan="{{ e($r['tunjangan_jabatan'] ?? 0) }}"
                data-tunjangan-transport="{{ e($r['tunjangan_transport'] ?? 0) }}"
                data-tunjangan-luar-kota="{{ e($r['tunjangan_luar_kota'] ?? 0) }}"
                data-tunjangan-masa-kerja="{{ e($r['tunjangan_masa_kerja'] ?? 0) }}"
                data-gaji-lembur="{{ e($r['gaji_lembur'] ?? 0) }}"
                data-tabungan-diambil="{{ e($r['tabungan_diambil'] ?? 0) }}"
                data-potongan-lain="{{ e($r['potongan_lain'] ?? 0) }}"
                data-potongan-tabungan="{{ e($r['potongan_tabungan'] ?? 0) }}"
                data-potongan-keterlambatan="{{ e($r['potongan_keterlambatan'] ?? 0) }}"
                data-note="{{ e($r['note'] ?? '') }}" data-reasonedit="{{ e($r['reasonedit'] ?? '') }}"
                data-bs-toggle="modal" data-bs-target="#modalEditPayroll">
                Edit
            </button>
        </td>
    </tr>
@endforeach
