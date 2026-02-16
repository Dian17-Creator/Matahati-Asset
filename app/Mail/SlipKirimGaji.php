<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlipKirimGaji extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $row;
    public $filePath;

    public function __construct($row, $filePath)
    {
        $this->row = $row;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject(
            'Slip Gaji ' . ($this->row->bulan ?? '') . ' - ' . ($this->row->user->cname ?? '')
        )
            ->view('emails.slip_gaji')
            ->with([
                'data' => [
                    'nama' => $this->row->user->cname ?? '',
                    'bulan' => $this->row->bulan ?? '',
                    'jabatan' => $this->row->user->jabatan->name ?? '',
                    'hari_masuk' => $this->row->hari_masuk ?? 0,
                    'tanggal_cetak' => now()->format('d/m/Y'),
                ]
            ])
            ->attach($this->filePath, [
                'as' => basename($this->filePath),
                'mime' => 'application/pdf',
            ]);
    }
}
