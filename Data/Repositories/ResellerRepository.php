<?php

namespace NW\WebService\References\Operations\Notification\Data\Repositories;

use NW\WebService\References\Operations\Notification\Data\Models\Reseller;

class ResellerRepository extends AbstractRepository
{
    protected function getModelClassName(): string
    {
        return Reseller::class;
    }

    protected function getTableName(): string
    {
        return 'Resellers';
    }

    public function getById(int $id): Reseller|null
    {
        if ($id === 0) {
            return null;
        }
        
        // some db search code..
        $searchResult = [];

        return $this->mapRowToModel($searchResult);
    }
}