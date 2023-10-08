<?php

namespace NW\WebService\References\Operations\Notification\Data\Transporters\Inputs;

use NW\WebService\References\Operations\Notification\Data\Enums\NotificationType;
use NW\WebService\References\Operations\Notification\Data\Models\Client;
use NW\WebService\References\Operations\Notification\Data\Models\Complaint;
use NW\WebService\References\Operations\Notification\Data\Models\Consumption;
use NW\WebService\References\Operations\Notification\Data\Models\Employee;
use NW\WebService\References\Operations\Notification\Data\Models\Reseller;
use NW\WebService\References\Operations\Notification\Data\Transporters\DTO\Differences;

class OperationInput
{
    public ?Reseller $reseller;
    public ?NotificationType $notificationType;
    public ?Client $client;
    public ?Employee $creator;
    public ?Employee $expert;
    public ?Differences $differences;
    public ?Consumption $consumption;
    public ?Complaint $complaint;
    public ?string $agreementNumber;
    public ?string $date;
}