<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use App\Exceptions\Http\ForbiddenException;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftAuthCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Exceptions\Http\UnauthorisedException;
use App\Entities\Accounts\Resources\AccountResource;
use App\Exceptions\Http\BadRequestException;
use App\Entities\Groups\Models\Group;

final class MinecraftAuthTokenController extends ApiController
{
    /**
     * Requests an URL that the user can click to link their PCB account
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validateRequest($request->all(), [
            'minecraft_uuid' => 'bail|required|string', // TODO: override UUID rule to allow UUIDs without hyphens
        ]);

        $uuid = $request->get('minecraft_uuid');
        $uuid = str_replace('-', '', $uuid);

        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)->first();
        
        if ($existingPlayer === null) {
            $existingPlayer = MinecraftPlayer::create([
                'uuid'         => $uuid,
                'account_id'   => null,
                'playtime'     => 0,
                'last_seen_at' => Carbon::now(),
            ]);
        }

        // Re-authenticating a Minecraft account when it's already authenticated is not currently
        // supported, as this might cause some unexpected results
        if ($existingPlayer->account_id !== null) {
            throw new ForbiddenException('already_authenticated', 'This UUID has already been authenticated');
        }

        // Invalidate any existing auth codes since only 1 should be alive
        MinecraftAuthCode::where('player_minecraft_id', $existingPlayer->getKey())->delete();

        $authCode = MinecraftAuthCode::create([
            'player_minecraft_id' => $existingPlayer->getKey(),
            'uuid'                => $uuid,
            'token'               => Str::uuid()->toString(),
            'expires_at'          => Carbon::now()->addMinutes(30),
        ]);

        $authCompletionRoute = route('front.auth.minecraft.token', ['token' => $authCode->token]);

        return [
            'data' => [
                'url' => $authCompletionRoute,
            ],
        ];
    }
    
    /**
     * Returns the PCB groups that the given UUID belongs to
     *
     * @param Request $request
     * @return void
     */
    public function show(Request $request, String $minecraftUUID)
    {
        $uuid = str_replace('-', '', $minecraftUUID);

        if (empty($uuid)) {
            throw new BadRequestException('bad_input', 'minecraft_uuid cannot be empty');
        }

        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)->first();

        if ($existingPlayer === null || $existingPlayer->account === null) {
            throw new UnauthorisedException('account_not_linked', 'This UUID has not been linked to a PCB account. Please complete the authorization flow first');
        }

        // If for some reason the user has no groups but has an account, assign them
        // to the Member group because that's the default non-Guest group
        $groups = $existingPlayer->account->groups;
        if (count($groups) === 0) {
            $defaultGroups = Group::where('is_default', 1)->get()->pluck('group_id');
            $existingPlayer->account->groups()->attach($defaultGroups);
            $existingPlayer->account->groups->push($defaultGroups);
        }
        
        return [
            'data' => new AccountResource($existingPlayer->account),
        ];
    }
}
