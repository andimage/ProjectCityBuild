<?php

namespace Domain\PlayerFetch\Listeners;

use Domain\ServerStatus\Events\ServerStatusFetched;

class ServerStatusFetchedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(ServerStatusFetched $event)
    {
    }
}
