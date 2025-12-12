<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Member System Configuration
    |--------------------------------------------------------------------------
    */

    // The guard to use for authentication
    'guard' => 'member',

    // Table names
    'tables' => [
        'members' => 'members',
        'profiles' => 'member_profiles',
    ],

    // Avatar storage settings
    'avatars' => [
        'disk' => 'public',
        'path' => 'avatars',
    ],
];