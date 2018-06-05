<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::model('city', \App\City::class);
        Route::model('plan', \App\Plan::class);
        Route::model('discipline', \App\Discipline::class);
        Route::model('menu_item', \App\MenuItem::class);
        Route::model('message', \App\Message::class);
        Route::model('practice', \App\Practice::class);
        Route::model('school', \App\School::class);
        Route::model('section', \App\Section::class);
        Route::model('setting', \App\Setting::class);
        Route::model('subtype', \App\Subtype::class);
        Route::model('theory', \App\Theory::class);
        Route::model('transaction', \App\Transaction::class);
        Route::model('variant', \App\Variant::class);
        Route::model('user', \App\User::class);
        Route::bind('page', function ($value) {
            return \App\Page::find($value);
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace("{$this->namespace}\Api")
             ->group(base_path('routes/api.php'));
    }
}
