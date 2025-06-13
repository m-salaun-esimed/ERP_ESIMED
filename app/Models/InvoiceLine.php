<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'line_order'
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
