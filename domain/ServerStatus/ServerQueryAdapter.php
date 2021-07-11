<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;

interface ServerQueryAdapter
{
    /**
     * Queries the given server for its current status.
     * @param null $port
     */
    public function query(string $ip, $port = null): ServerQueryResult;
}
