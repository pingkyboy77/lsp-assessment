<?php

namespace App\Providers;

use App\Models\Apl02;
use App\Policies\Apl02Policy;
use App\Observers\Apl02Observer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Observers\Apl02ElementAssessmentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Apl02::class, Apl02Policy::class);

        // Register observers
        \App\Models\Apl02::observe(Apl02Observer::class);
        \App\Models\Apl02ElementAssessment::observe(Apl02ElementAssessmentObserver::class);
    }
}
