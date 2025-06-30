<?php

namespace App\Models;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
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
        'city',
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

    use HasRelationships;

    public function invoices()
    {
        return $this->hasManyDeep(
            \App\Models\Invoice::class,
            [\App\Models\Project::class, \App\Models\Quote::class],
            [
                'customer_id', 
                'project_id',
                'quote_id'
            ],
            [
                'id',
                'id',
                'id'
            ]
        );
    }
}
