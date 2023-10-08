<?php

namespace NW\WebService\References\Operations\Notification\Data\Models;
use NW\WebService\References\Operations\Notification\Data\Enums\ContractorType;

class Client extends AbstractModel
{
    public ?string $name;
    public ?string $email;
    public ?bool $mobileNotifications = false;
    public ?ContractorType $type;

    public function getFullName(): string
    {
        return sprintf("%s %s", $this->name, $this->id);
    }
}