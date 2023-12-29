<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DuplicateRemovedNotification extends Mailable
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
    return $this->subject('Duplicate Clients Removed')
                ->view('emails.duplicate_removed_notification')
                ->with(['importSummary' => $this->importSummary]);
}
}
