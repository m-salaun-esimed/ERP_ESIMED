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
}
