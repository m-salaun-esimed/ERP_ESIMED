<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
     protected $table = 'project_statuses';
     protected $fillable = ['name'];
}
