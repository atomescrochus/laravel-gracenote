<?php

namespace Atomescrochus\Gracenote\Exceptions;

use Exception;

class UsageErrors extends Exception
{
    public static function searchType()
    {
        return new static("This search type is invalid.");
    }
}
