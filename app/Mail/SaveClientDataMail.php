<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaveClientDataMail extends Mailable
{
    use Queueable, SerializesModels;

    public $importSummary;

    /**
     * Create a new message instance.
     *
     * @param array $importSummary
     * @return void
     */
    public function __construct($importSummary)
    {
        $this->importSummary = $importSummary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
{
    return $this->subject('Import Client Data Successfully')
                ->view('emails.save-client-data')
                ->with(['importSummary' => $this->importSummary]);
}
}
