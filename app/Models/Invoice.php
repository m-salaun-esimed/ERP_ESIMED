<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Invoice extends Model
{
    protected $fillable = [
        'status',
        'project_id',
        'invoice_number',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $lastInvoice = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;

            $invoice->invoice_number = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function getTotalCostAttribute(): float
    {
        return $this->invoiceLines->sum(function ($line) {
            return $line->quantity * $line->unit_price;
        });
    }

    public function getTotalCostFormattedAttribute(): string
    {
        return Number::currency($this->total_cost, 'EUR');
    }
}
