<?php

namespace NW\WebService\References\Operations\Notification\Data\Models;

class Reseller extends AbstractModel
{
    // если getResellerEmailFrom ищет в бд, то репозиторий заполнит email из результата поиска по resellerId
    public ?string $email;
}