<?php

namespace App\Providers;

use App\Lessons\LessonRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One shared lesson repository, pointed at the course folder.
        $this->app->singleton(LessonRepository::class, fn () => new LessonRepository(config('course.path')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Catch N+1 queries while developing (see the "Relationships" lesson).
        Model::preventLazyLoading(! $this->app->isProduction());

        // public/css/app.css styles the plain .pagination markup of these views.
        Paginator::useBootstrapFive();
    }
}
