<?php

namespace App\Console\Commands;

use App;
use App\Entities\Servers\Models\Server;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\Repositories\ServerStatusRepository;
use Domain\ServerStatus\ServerQueryAdapterFactory;
use Domain\ServerStatus\ServerQueryService;
use Illuminate\Console\Command;

final class ServerQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:query {--background} {--id=*} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queries the a given server for its current status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shouldRunInBackground = $this->option('background') ? true : false;

        if ($this->option('all')) {
            $this->queryAllServers($shouldRunInBackground);

            return;
        }

        $serverIds = $this->option('id');
        if (count($serverIds) === 0) {
            $this->error('You must specify either --id=* or --all');

            return;
        }

        $servers = Server::whereIn('server_id', $serverIds)->get();
        foreach ($servers as $server) {
            $this->queryServer($server, $shouldRunInBackground);
        }
    }

    private function queryAllServers(bool $shouldRunInBackground)
    {
        $servers = Server::where('is_querying', true)->get();

        foreach ($servers as $server) {
            $this->queryServer($server, $shouldRunInBackground);
        }
    }

    private function queryServer(Server $server, bool $shouldRunInBackground)
    {
        $adapterFactory = App::make(ServerQueryAdapterFactory::class);
        $queryService = new ServerQueryService($adapterFactory);

        if ($shouldRunInBackground) {
            $this->info('Starting server query on background queue ['.$server->getAddress().']');
            $queryService->query($server);

            return;
        }
        try {
            $result = $queryService->querySynchronously($server, App::make(ServerStatusRepository::class));
            dump($result->toArray());
        } catch (UnsupportedGameException $e) {
            $this->error('Querying '.$server->gameType()->name().' game type is unsupported');
        }
    }
}
