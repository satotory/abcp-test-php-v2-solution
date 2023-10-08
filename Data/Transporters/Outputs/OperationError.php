<?php

namespace NW\WebService\References\Operations\Notification\Data\Transporters\Outputs;

class OperationError
{
    public int $status = 150;

    public function __construct(
        public string $message, 
        public int $code
    ) {}
}