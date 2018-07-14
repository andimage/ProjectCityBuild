<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Donations\Services\DonationStatsService;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Modules\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;

class HomeController extends WebController {

    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(DonationStatsService $donationStatsService) {
        $this->donationStatsService = $donationStatsService;
    }

    public function getView() {
        $donations = $this->donationStatsService->getAnnualPercentageStats();

        // combine unique Minecraft player count + forum accounts with
        // no game account linked (due to SMF import)
        $playerCount = Cache::remember('front.player_count', 10, function() {
            $minecraftPlayers = MinecraftPlayer::count();
            $unlinkedAccounts = Account::whereDoesntHave('minecraftAccount')->count();
            
            return $minecraftPlayers + $unlinkedAccounts;
        });

        return view('front.pages.home', [
            'donations'     => $donations,
            'playerCount'   => $playerCount,
        ]);
    }
}
