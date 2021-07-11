<?php

namespace Tests\Unit\Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\ServerQueryAdapter;

final class ServerQueryAdapterMock implements ServerQueryAdapter
{
    private ?ServerQueryResult $result;

    public function __construct(?ServerQueryResult $result = null)
    {
        $this->result = $result;
    }

    public function query(string $ip, $port = null): ServerQueryResult
    {
        if ($this->result == null) {
            throw new \Exception('No result mocked');
        }

        return $this->result;
    }
}
