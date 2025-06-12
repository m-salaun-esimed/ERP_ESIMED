<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = true;
    
    protected $fillable = [
        'name',
        'status',
        'customer_id',
        'date_started',
        'date_end',
        'status_project_id'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function statusProject()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_project_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
