<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Activity::saving(function (Activity $activity) {
            // Associer l'utilisateur connecté aux logs d'activité
            if (Auth::check()) {
                $activity->causer_id = Auth::id();
                $activity->causer_type = get_class(Auth::user());
            }
        });
    }

    public function register()
    {
        Paginator::useTailwind();
    }
}
