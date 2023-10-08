<?php

namespace NW\WebService\References\Operations\Notification\Exceptions;

use Exception;

class SendingMailException extends Exception
{
    const code = 500;

    public function __construct($message = "", $previous = null) 
    {
        parent::__construct($message, self::code, $previous);
    }
}