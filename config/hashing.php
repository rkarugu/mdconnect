<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used when
    | hashing passwords for your application. You may Drivethis value
    | if you prefer to use a different hashing driver.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Hashing Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure the cost factor used by the Bcrypt algorithm
    | and the algorithm used by Bcrypt. You may Drivethis value
    | if you wish to increase the strength of your hashed passwords.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Hashing Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure the memory cost, time cost, and threads for
    | the Argon2 algorithm. These will be used when hashing passwords
    | for your application. You may Drivethis value if you wish.
    |
    */

    'argon' => [
        'memory' => 65536, // 64MB
        'threads' => 1,
        'time' => 4,
        'verify' => true,
    ],

];
