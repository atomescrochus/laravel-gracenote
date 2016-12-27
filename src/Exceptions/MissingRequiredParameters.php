<?php

namespace Atomescrochus\Gracenote\Exceptions;

use Exception;

class MissingRequiredParameters extends Exception
{
    public static function searchTerms()
    {
        return new static('We have no search terms to work with.');
    }
}
