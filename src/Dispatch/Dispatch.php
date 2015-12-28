<?php

namespace Kregel\Dispatch;

use Illuminate\Support\ServiceProvider;
use Kregel\Dispatch\Models\Jurisdiction;
use App\Models\Roles\Permission;

class Dispatch extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
    }

    /**
     * Preform booting of services...
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            $this->app->router->group(['namespace' => 'Kregel\Dispatch\Http\Controllers'], function ($router) {
                require __DIR__.'/Http/routes.php';
            });
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dispatch');
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dispatch'),
        ], 'views');
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('kregel/dispatch.php'),
        ], 'config');

        Jurisdiction::creating(function ($jurisdiction) {
            $perm = Permission::create([
                'name' => 'view-'.str_slug($jurisdiction->name),
                'display_name' => 'View '.$jurisdiction->name,
                'description' => 'This permission will let the user view '.strtolower($jurisdiction->name),
            ]);
            if (\Auth::check()) {
                $user_model = config('auth.model');
                $user = $user_model::find(\Auth::user()->id);
                $user->perms()->attach($perm->id);
            }
        });
        Jurisdiction::created(function ($jurisdiction) {
            if (\Auth::check()) {
				if(!\Auth::user()->jurisdiction->contains($jurisdiction->id))
					\Auth::user()->jurisdiction()->attach($jurisdiction->id);
            }
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
