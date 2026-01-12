<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Illuminate\Mail\Events\MessageSending::class => [
            \App\Listeners\AddSesTenantHeader::class,
        ],
    ];

    public function boot() {
        parent::boot();
    }
}
