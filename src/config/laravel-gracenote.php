<?php

return [

    /*
     * You can get those values from your Gracenote developer account
     */
    'client_id' => env('GRACENOTE_CLIENT_ID'),
    'client_tag' => env('GRACENOTE_CLIENT_TAG'),

    /*
     * If you don't have it, you can add the above values to your environment file,
     * then run ` php artisan gracenote:user-id` to get the required value of this property.
     */
    'user_id' => env('GRACENOTE_USER_ID'),

    /**
     * By default, the cache is set to 60 minutes
     */
    'cache' => 60,
];
