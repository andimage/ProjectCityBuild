<?php

namespace Domain\ServerStatus\Jobs;

use App;
use App\Entities\Servers\Models\Server;
use Domain\ServerStatus\Repositories\ServerStatusRepository;
use Domain\ServerStatus\ServerQueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ServerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Server $server;

    /**
     * Create a new job instance.
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Domain\ServerStatus\Exceptions\UnsupportedGameException
     */
    public function handle(ServerQueryService $queryService)
    {
        $queryService->querySynchronously(
            $this->server,
            App::make(ServerStatusRepository::class)
        );
    }
}
