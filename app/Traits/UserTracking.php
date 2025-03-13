<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTracking
{
    public static function bootUserTracking()
    {
        static::creating(function ($model) {
            if (!isset($model->created_by) && Auth::check()) {
                $model->created_by = Auth::id();
            }
        });
    }
}
