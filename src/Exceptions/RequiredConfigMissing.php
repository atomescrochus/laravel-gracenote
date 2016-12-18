<?php

namespace Atomescrochus\Gracenote\Exceptions;

use Exception;

class RequiredConfigMissing extends Exception
{
    public static function cantFindUserId()
    {
        return new static("Could not find the Gracenote user id, check the config or environment file.");
    }

    public static function cantFindClientId()
    {
        return new static("Could not find the Gracenote client id, check the config or environment file.");
    }

    public static function cantFindClientTag()
    {
        return new static("Could not find the Gracenote client tag, check the config or environment file.");
    }
}
