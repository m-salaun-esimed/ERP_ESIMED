<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'user_id',
        'created_at',
        'contact_name',
        'phone_number',
        'email',
        'address',
    ];

    protected static function booted()
    {
        static::deleting(function ($customer) {
            if ($customer->projects()->exists()) {
                throw new \Exception("Impossible de supprimer ce client car il a des projets associés.");
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(
            \App\Models\Invoice::class,
            \App\Models\Project::class,
            'customer_id',       // Foreign key on the projects table...
            'quote_id',          // Foreign key on the invoices table...
            'id',                // Local key on the customers table...
            'id'                 // Local key on the projects table...
        )->join('quotes', 'projects.id', '=', 'quotes.project_id')
        ->whereColumn('quotes.id', 'invoices.quote_id');
    }

}
