<?php

namespace NW\WebService\References\Operations\Notification\Data\Enums;

enum OperationStatus: int
{
    case COMPLETED  = 0;
    case PENDING    = 1;
    case REJECTED   = 2;
}