<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteLine extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'description',
        'quantity',
        'unit_price',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    protected static function booted()
    {
        static::saving(function ($line) {
            $line->line_total = $line->unit_price * $line->quantity;
        });
    }
}
