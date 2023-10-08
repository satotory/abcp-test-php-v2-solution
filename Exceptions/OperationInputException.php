<?php

namespace NW\WebService\References\Operations\Notification\Exceptions;

use Exception;

class OperationInputException extends Exception
{
    const code = 400;

    public function __construct($message = "", $previous = null) 
    {
        parent::__construct($message, self::code, $previous);
    }
}