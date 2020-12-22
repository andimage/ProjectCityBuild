<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Http\UnauthorisedException;
use App\Http\ApiController;
use App\Library\Discord\DiscordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class DeployController extends ApiController
{
    /**
     * @var DiscordNotification
     */
    private $discord;

    public function __construct(DiscordNotification $discord)
    {
        $this->discord = $discord;
    }

    public function deploy(Request $request)
    {
        $branch = config('deployment.branch');
        $key = config('deployment.key');

        if ($key === null) {
            $this->discord->sendToChannel('Deployment', '❌ Deployment failed: No deployment key set in .env');
            throw new \Exception('No deployment key setup');
        }

        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');

        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $key, false);

        if (! hash_equals($githubHash, $localHash)) {
            throw new UnauthorisedException('bad_deploy_key', 'Invalid deployment key specified');
        }

        $decodedPayload = json_decode($githubPayload, true);

        $branch = $decodedPayload['ref'];
        if ($branch !== 'refs/heads/master') {
            Log::info('Skipping deployment for branch: '.$branch);
            return;
        }

        $this->discord->sendToChannel('Deployment', '🕒 Deployment has begun...');

        try {
            chdir(base_path() . '/../../');
            echo shell_exec('./deploy.sh 2>&1');
        } catch (\Exception $e) {
            $this->discord->sendToChannel('Deployment', '❌ Deployment failed: '.$e->getMessage());
            Log::fatal($e);
        }
    }
}
