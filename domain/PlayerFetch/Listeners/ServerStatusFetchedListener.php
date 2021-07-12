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
     * @param ServerStatusFetched $event
     * @return void
     */
    public function handle(ServerStatusFetched $event)
    {

    }
}
