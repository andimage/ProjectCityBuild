<?php

namespace Domain\PlayerFetch;

use Domain\PlayerAliasFetch\Listeners\ServerStatusFetchedListener;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Illuminate\Support\ServiceProvider;

class PlayerFetchProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ServerStatusFetched::class => [ServerStatusFetchedListener::class],
    ];
}
