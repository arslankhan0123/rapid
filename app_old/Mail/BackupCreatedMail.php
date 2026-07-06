<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadUrl;
    public $zipFilePath;

    /**
     * Create a new message instance.
     */
    public function __construct($downloadUrl, $zipFilePath)
    {
        $this->downloadUrl = $downloadUrl;
        $this->zipFilePath = $zipFilePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Database Backup Created')
            ->view('emails.db_backup.backup')
            ->with(['downloadUrl' => $this->downloadUrl])
            ->attach($this->zipFilePath, [
                'as'   => basename($this->zipFilePath),
                'mime' => 'application/zip',
            ]);
    }
}
