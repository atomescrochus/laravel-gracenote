<?php

return [

    /*
     * You can get those values from your Gracenote developer account
     */
    'client_id' => env('GRACENOTE_CLIENT_ID'),
    'client_tag' => env('GRACENOTE_CLIENT_TAG'),

    /*
     * You will have to get it by yourself, for now.
     */
    'user_id' => env('GRACENOTE_USER_ID'),

    /**
     * By default, the cache is set to 60 minutes
     */
    'cache' => 60,
];
