<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KartuStokMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $data;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    /**
     * Email subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kartu Stok Harian - ' . $this->date,
        );
    }

    /**
     * Email content (view)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.kartu_stok',
            with: [
                'data' => $this->data,
                'date' => $this->date,
            ],
        );
    }

    /**
     * Attachments (optional)
     */
    public function attachments(): array
    {
        return [];
    }
}
