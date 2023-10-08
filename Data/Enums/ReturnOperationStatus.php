<?php

namespace NW\WebService\References\Operations\Notification\Data\Enums;

enum ReturnOperationStatus: int
{
    case TYPE_NEW       = 1;
    case TYPE_CHANGE    = 2;
}