<?php

namespace Atomescrochus\Gracenote\Facades;

use Illuminate\Support\Facades\Facade;

class Gracenote extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gracenote';
    }
}