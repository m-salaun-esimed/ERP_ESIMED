<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_status_id',
        'quote_id',
        'invoice_number',
        'payment_type',
        'issue_date',
        'due_date',
        'payment_date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function status()
    {
        return $this->belongsTo(InvoiceStatus::class, 'invoice_status_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {

        $quote = Quote::find($invoice->quote_id);

        if (!$quote) {
            throw new \Exception("La quote associée n'existe pas.");
        }

        if ($invoice->issue_date >= $quote->expire_on) {
            throw new \Exception("La date de création de la facture doit être strictement antérieure à la date d'expiration de la quote.");
        }

            $lastInvoice = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;

            $invoice->invoice_number = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function getTotalCostAttribute(): float
    {
        return $this->invoiceLines->sum(function ($line) {
            return $line->line_total;
        });
    }
}
