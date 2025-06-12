<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'description',
        'quantity',
        'unit_price',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
