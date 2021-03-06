<?php

namespace App\Http\Controllers;

use App\Entities\Bans\Models\GameBan;
use App\Http\WebController;
use Illuminate\Http\Request;

final class BanlistController extends WebController
{
    public function index(Request $request)
    {
        $bans = GameBan::where('is_active', 1)->with(['bannedPlayer', 'staffPlayer', 'staffPlayer.aliases'])->latest();

        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $bans = GameBan::search($query)->constrain($bans);
        } else {
            $query = '';
        }

        $bans = $bans->paginate(50);

        return view('front.pages.banlist')->with(compact('bans', 'query'));
    }
}
