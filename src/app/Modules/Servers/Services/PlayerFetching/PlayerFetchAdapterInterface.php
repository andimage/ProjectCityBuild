<?php
namespace App\Modules\Servers\Services\PlayerFetching;

interface PlayerFetchAdapterInterface {

    /**
     * Returns the unique identifiers for a list of players
     *
     * @param array $key
     * @return array
     */
    public function getUniqueIdentifiers(array $key = []) : array;

}