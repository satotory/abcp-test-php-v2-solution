<?php

namespace NW\WebService\References\Operations\Notification\Data\Repositories;

use NW\WebService\References\Operations\Notification\Data\Models\Employee;

class EmployeeRepository extends AbstractRepository
{
    protected function getModelClassName(): string
    {
        return Employee::class;
    }

    protected function getTableName(): string
    {
        return 'Employees';
    }

    public function getById(int $id): Employee|null
    {
        if ($id === 0) {
            return null;
        }
        
        // some db search code..
        $searchResult = [];

        return $this->mapRowToModel($searchResult);
    }
}