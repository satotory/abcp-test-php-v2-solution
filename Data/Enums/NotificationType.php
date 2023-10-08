<?php

namespace NW\WebService\References\Operations\Notification\Data\Enums;

enum NotificationType: int
{
    case TYPE_NEW       = 1;
    case TYPE_CHANGE    = 2;
    
    // additional types 
    case TYPE_DONE      = 3;
    case TYPE_REMOVE    = 4;
}