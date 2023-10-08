<?php

namespace NW\WebService\References\Operations\Notification\Data\Repositories;

use NW\WebService\References\Operations\Notification\Data\Models\Client;

class ClientRepository extends AbstractRepository
{
    protected function getModelClassName(): string
    {
        return Client::class;
    }

    protected function getTableName(): string
    {
        return 'Clients';
    }

    public function getById(int $id): Client|null
    {
        if ($id === 0) {
            return null;
        }

        // some db search code..
        $searchResult = [];

        return $this->mapRowToModel($searchResult);
    }
}