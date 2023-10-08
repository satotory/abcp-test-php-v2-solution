<?php

namespace NW\WebService\References\Operations\Notification\Data\Enums;

enum NotificationEvents: string
{
    case CHANGE_RETURN_STATUS = 'changeReturnStatus';
    case NEW_RETURN_STATUS    = 'newReturnStatus';
}