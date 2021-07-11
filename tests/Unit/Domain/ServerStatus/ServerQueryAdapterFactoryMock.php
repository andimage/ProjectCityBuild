<?php

namespace Tests\Unit\Domain\ServerStatus;

use App\Entities\GameType;
use Domain\ServerStatus\ServerQueryAdapter;
use Domain\ServerStatus\ServerQueryAdapterFactoryContract;

final class ServerQueryAdapterFactoryMock implements ServerQueryAdapterFactoryContract
{
    private ServerQueryAdapter $adapter;

    public function __construct(ServerQueryAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function make(GameType $gameType): ServerQueryAdapter
    {
        return $this->adapter;
    }
}
