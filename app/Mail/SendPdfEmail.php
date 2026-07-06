<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPdfEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $bodyText;
    public $link;
    protected $pdf;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param string $bodyText
     * @param string $link
     * @param string $pdf
     */
    public function __construct($bodyText, $link, $pdf, $subject)
    {
        $this->bodyText = $bodyText;
        $this->link = $link;
        $this->pdf = $pdf;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invoice.pdf_email') // Your email blade file
            ->subject($this->subject)
            ->attachData($this->pdf, $this->subject . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
