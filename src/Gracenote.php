<?php

namespace Atomescrochus\Gracenote;

use Atomescrochus\Gracenote\Exceptions\RequiredConfigMissing;

class Gracenote
{

    public $client_id;
    public $client_tag;
    public $user_id;

    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        $this->setGracenoteKeys();
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function echoPhrase($phrase)
    {
        return $phrase;
    }

    /**
     * This will try to set required information to be found in config/env file,
     * and throw an exception if nothing is found.
     */
    private function setGracenoteKeys()
    {
        if(empty(config('laravel-gracenote.client_id'))){
            throw RequiredConfigMissing::cantFindClientId();
        }

        if(empty(config('laravel-gracenote.client_tag'))){
            throw RequiredConfigMissing::cantFindClientTag();
        }

        if(empty(config('laravel-gracenote.user_id'))){
            throw RequiredConfigMissing::cantFindUserId();
        }

        $this->client_id = config('laravel-gracenote.client_id');
        $this->client_tag = config('laravel-gracenote.client_tag');
        $this->user_id = config('laravel-gracenote.user_id');
    }
}
