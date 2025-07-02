<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverdueInvoicesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $overdueInvoices;

    public function __construct($user, $overdueInvoices)
    {
        $this->user = $user;
        $this->overdueInvoices = $overdueInvoices;
    }

    public function build()
    {
        return $this->subject('Factures en retard')
                    ->markdown('emails.overdue_invoices');
    }
}
