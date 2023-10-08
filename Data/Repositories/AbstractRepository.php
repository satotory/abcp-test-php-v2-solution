<?php

namespace NW\WebService\References\Operations\Notification\Data\Repositories;

use NW\WebService\References\Operations\Notification\Data\Models\AbstractModel;
use NW\WebService\References\Operations\Notification\Exceptions\RepositoryException;

abstract class AbstractRepository
{
    abstract protected function getTableName(): string;
    
    abstract protected function getModelClassName(): string;

    public function getById(int $id): AbstractModel|null
    {
        // some db search code...

        return null;
    }

    protected function mapRowToModel(array $row): AbstractModel
    {
        $model = $this->getModel();

        foreach ($row as $field => $value) {
            $model->$field = $value;
        }

        return $model;
    }


    protected function getModel(): AbstractModel
    {
        $class = $this->getModelClassName();

        if (is_a($class, AbstractModel::class, true)) {
            throw new RepositoryException('Model should be AbstractModel');
        }

        return new $class;
    }
}