<?php

declare(strict_types=1);

return [

    'ledger' => [

        /*
        |--------------------------------------------------------------------------
        | Ledger Implementation
        |--------------------------------------------------------------------------
        |
        | Define the Ledger implementation.
        |
        */

        'implementation' => Altek\Accountant\Models\Ledger::class,

        /*
        |--------------------------------------------------------------------------
        | Ledger Threshold
        |--------------------------------------------------------------------------
        |
        | Specify a cutoff for the number of Ledger records a model can have.
        | Zero means unlimited.
        |
        */

        'threshold' => 0,

        /*
        |--------------------------------------------------------------------------
        | Ledger Driver
        |--------------------------------------------------------------------------
        |
        | The default driver used to store Ledger records.
        |
        */

        'driver' => 'database',

        /*
        |--------------------------------------------------------------------------
        | Ledger Date Serialisation Format
        |--------------------------------------------------------------------------
        |
        | The default date serialisation format to be used by the model.
        |
        */

        'date_format' => 'Y-m-d H:i:s',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resolver Implementations
    |--------------------------------------------------------------------------
    |
    | Define the Context, IP Address, URL, User Agent and User resolver
    | implementations.
    |
    */

    'resolvers' => [
        'context'    => Altek\Accountant\Resolvers\ContextResolver::class,
        'ip_address' => Altek\Accountant\Resolvers\IpAddressResolver::class,
        'url'        => Altek\Accountant\Resolvers\UrlResolver::class,
        'user_agent' => Altek\Accountant\Resolvers\UserAgentResolver::class,
        'user'       => Altek\Accountant\Resolvers\UserResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Recording contexts
    |--------------------------------------------------------------------------
    |
    | Define the contexts in which recording should take place.
    |
    */

    'contexts' => Altek\Accountant\Context::WEB | Altek\Accountant\Context::CLI,

    /*
    |--------------------------------------------------------------------------
    | Notary Implementation
    |--------------------------------------------------------------------------
    |
    | The default Notary implementation.
    |
    */

    'notary' => Altek\Accountant\Notary::class,

    /*
    |--------------------------------------------------------------------------
    | Recordable Events
    |--------------------------------------------------------------------------
    |
    | The events that trigger a new Ledger record.
    |
    */

    'events' => [
        'created',
        'updated',
        'restored',
        'deleted',
        'forceDeleted',
    ],

    /*
    |--------------------------------------------------------------------------
    | User MorphTo relation prefix & default Guards
    |--------------------------------------------------------------------------
    |
    | Define the morph prefix and which authentication guards the User resolver
    | should use.
    |
    */

    'user' => [
        'prefix' => 'accountable',
        'guards' => [
            'web',
            'api',
        ],
    ],

];
