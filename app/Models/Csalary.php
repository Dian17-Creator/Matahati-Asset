<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Csalary extends Model
{
    use HasFactory;

    protected $table = 'csalary';

    // primary key default 'id' — sesuai screenshot (bigint unsigned auto_increment)
    // jika berbeda, atur protected $primaryKey = '...';

    // timestamps default true — sesuai adanya created_at & updated_at
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'period_year',
        'period_month',
        'jabatan',
        'jumlah_masuk',
        'jenis_gaji',
        'contract_nominal',
        'gaji_harian',
        'gaji_pokok',
        'tunjangan_makan',
        'tunjangan_jabatan',
        'tunjangan_transport',
        'tunjangan_luar_kota',
        'tunjangan_masa_kerja',
        'gaji_lembur',
        'tabungan_diambil',
        'potongan_lain',
        'potongan_tabungan',
        'potongan_keterlambatan',
        'total_gaji',
        'note',
        'keterangan_absensi',
        'reasonedit',
    ];

    /**
     * Casts - memastikan kolom numeric diambil sebagai float / decimal
     */
    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'jumlah_masuk' => 'integer',
        'jenis_gaji' => 'string',
        'contract_nominal' => 'decimal:2',
        'gaji_harian' => 'decimal:2',
        'gaji_pokok' => 'decimal:2',
        'tunjangan_makan' => 'decimal:2',
        'tunjangan_jabatan' => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_luar_kota' => 'decimal:2',
        'tunjangan_masa_kerja' => 'decimal:2',
        'gaji_lembur' => 'decimal:2',
        'tabungan_diambil' => 'decimal:2',
        'potongan_lain' => 'decimal:2',
        'potongan_tabungan' => 'decimal:2',
        'potongan_keterlambatan' => 'decimal:2',
        'total_gaji' => 'decimal:2',
    ];

    /**
     * Relasi ke user (muser)
     * NOTE: model Muser di projectmu bernama 'muser' (sesuaikan namespace dan nama class jika berbeda)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\muser::class, 'user_id', 'nid');
    }

    /**
     * Scope untuk periode (tahun/bulan)
     */
    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('period_year', $year)->where('period_month', $month);
    }

    /**
     * Hitung total tunjangan (helper)
     */
    public function getTotalTunjanganAttribute()
    {
        return (float) (
            ($this->tunjangan_makan ?? 0)
            + ($this->tunjangan_jabatan ?? 0)
            + ($this->tunjangan_transport ?? 0)
            + ($this->tunjangan_luar_kota ?? 0)
            + ($this->tunjangan_masa_kerja ?? 0)
        );
    }

    /**
     * Hitung total potongan (helper)
     */
    public function getTotalPotonganAttribute()
    {
        return (float) (
            ($this->potongan_lain ?? 0)
            + ($this->potongan_tabungan ?? 0)
            + ($this->tabungan_diambil ?? 0)
            + ($this->potongan_keterlambatan ?? 0)
        );
    }

    /**
     * Jika kolom gaji_pokok kosong, hitung berdasarkan gaji_harian * jumlah_masuk
     */
    public function getComputedGajiPokokAttribute()
    {
        $harian = (float) ($this->gaji_harian ?? 0);
        $masuk = (int) ($this->jumlah_masuk ?? 0);

        return round($harian * $masuk, 2);
    }

    /**
     * Total gaji kotor (gaji pokok + tunjangan) — sebelum potongan
     */
    public function getGajiKotorAttribute()
    {
        $pokok = (float) ($this->gaji_pokok ?? $this->computed_gaji_pokok);
        return round($pokok + $this->total_tunjangan, 2);
    }

    /**
     * Gaji bersih (final) : bruto - potongan
     */
    public function getGajiBersihAttribute()
    {
        return round($this->gaji_kotor - $this->total_potongan, 2);
    }

    /**
     * Format rupiah (helper view)
     * contoh: $row->formatRupiah('gaji_bersih')
     */
    public function formatRupiah($field, $prefix = 'Rp ')
    {
        $val = $this->{$field} ?? 0;
        return $prefix . number_format($val, 0, ',', '.');
    }
}
