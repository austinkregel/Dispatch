<?php

namespace Kregel\Dispatch;

use Kregel\Dispatch\Commands\CheckTickets;
use Illuminate\Support\ServiceProvider;
use Kregel\Dispatch\Commands\EmailTicketInfo;
use Kregel\Dispatch\Commands\SendEmails;
use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\Dispatch\Models\Ticket;

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
        // Register some commands here...
        $this->app->singleton('command.dispatch.check.tickets', function ($app) {
            return new CheckTickets();
        });
        $this->commands('command.dispatch.check.tickets');
        // Register some commands here...
        $this->app->singleton('command.dispatch.send.emails', function ($app) {
            return new SendEmails();
        });
        $this->commands('command.dispatch.send.emails');
        // Register some commands here...
        $this->app->singleton('command.dispatch.email.tickets', function ($app) {
            return new  EmailTicketInfo();
        });
        $this->commands('command.dispatch.email.tickets');
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
            __DIR__.'/../resources/images' => storage_path('app/media/'),
        ], 'images');
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('kregel/dispatch.php'),
        ], 'config');

        //Ticket::updating(function ($ticket) {
        //    $ticket->adjust();
        //    $ticket->mailUsersUpdate();
        //});
        //Ticket::created(function($ticket){
        //    // We need to update the owner of the ticket,
        //    // Those who are assigned to it, and those
        //    // who commented on it of the new ticket
        //    $ticket->mailUsers();
        //});
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.dispatch.check.tickets',
            'command.dispatch.check.jurisdiction',
            'command.dispatch.email.tickets',
        ];
    }
}
