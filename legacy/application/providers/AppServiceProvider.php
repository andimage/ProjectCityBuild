<?php

namespace Application\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Entities\Players\Models\MinecraftPlayer;
use Illuminate\Support\Facades\View;
use Schema;
use Entities\Donations\Models\Donation;
use Entities\Payments\AccountPaymentType;
use Interfaces\Web\Composers\MasterViewComposer;
use Entities\GamePlayerType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // we don't want to store namespaces
        // in the database so we'll map them
        // to unique keys instead
        Relation::morphMap([
            'minecraft_player' => MinecraftPlayer::class,
            AccountPaymentType::Donation => Donation::class,
            GamePlayerType::Minecraft => MinecraftPlayer::class,
        ]);

        // bind the master view composer to the master view template
        View::composer('front.layouts.master', MasterViewComposer::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}
}