<?php

namespace Atomescrochus\Gracenote\Exceptions;

use Exception;

class UsageErrors extends Exception
{
    public static function searchType()
    {
        return new static('This search type is invalid.');
    }

    public static function searchMode()
    {
        return new static('This search type is invalid. Options are SINGLE_BEST or SINGLE_BEST_COVER.');
    }
}
