<?php

namespace App\Mail;

use App\Models\SupplyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplyRequestSlip extends Mailable
{
    use Queueable, SerializesModels;

    public $items;
    public $user;
    public $status;
    public $reason;

    /**
     * Create a new message instance.
     * 
     * @param \Illuminate\Support\Collection $items
     * @param string $status
     * @param string|null $reason
     */
    public function __construct($items, $status = 'pending', $reason = null)
    {
        $this->items = $items;
        $this->user = $items->first()->user;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Supply Request - ' . ucfirst($this->status);
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supply_request_slip',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
