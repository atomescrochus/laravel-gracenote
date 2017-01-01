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
        return new static('This search mode is invalid. Options are SINGLE_BEST or SINGLE_BEST_COVER.');
    }

    public static function queryType()
    {
        return new static('This query type is invalid. Options are "album_search" or "album_fetch".');
    }
}
