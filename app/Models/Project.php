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
        return $this->hasManyThrough(
            \App\Models\Invoice::class,
            \App\Models\Quote::class,
            'project_id', // Foreign key on quotes
            'quote_id',   // Foreign key on invoices
            'id',         // Local key on projects
            'id'          // Local key on quotes
        );
    }

    public function statusProject()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_project_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function paidInvoices()
    {
        return $this->invoices()->where('invoice_status_id', 3);
    }

    public function getTotalPaidInvoicesAmountAttribute()
    {
        return $this->paidInvoices()
            ->with('invoiceLines')
            ->get()
            ->flatMap->invoiceLines
            ->sum(function ($line) {
                return $line->unit_price * $line->quantity;
            });
    }
}
