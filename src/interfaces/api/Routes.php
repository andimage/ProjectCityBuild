<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('bans')->group(function () {
    Route::post('list', 'BanController@getBanList');
    
    Route::middleware('auth.token.server')->group(function () {
        Route::post('store/ban', 'BanController@storeBan');
        Route::post('store/unban', 'BanController@storeUnban');
        Route::post('status', 'BanController@getUserStatus');
        Route::post('history', 'BanController@getUserBanHistory');
    });
});

Route::prefix('servers')->group(function () {
    Route::get('all', 'ServerController@getAllServers');
});

Route::post('discord/sync', 'DiscordSyncController@getRank');

Route::post('minecraft/authenticate', 'TempMinecraftController@authenticate');