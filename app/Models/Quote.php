<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'status_id',
        'quote_number',
        'project_id',
        'created_at',
        'expires_on'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function statusQuote()
    {
        return $this->belongsTo(\App\Models\QuoteStatus::class, 'status_id');
    }

    public function quoteLines()
    {
        return $this->hasMany(QuoteLine::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $lastInvoice = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;

            $invoice->quote_number = 'Q-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
