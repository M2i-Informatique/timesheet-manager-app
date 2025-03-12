<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projectable extends Model
{
    protected $fillable = ['project_id', 'projectable_id', 'projectable_type'];
}
