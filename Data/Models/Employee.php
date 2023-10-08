<?php

namespace NW\WebService\References\Operations\Notification\Data\Models;

class Employee extends AbstractModel
{
    public ?string $name;
    
    public function getFullName(): string
    {
        return sprintf("%s %s", $this->name, $this->id);
    }
}