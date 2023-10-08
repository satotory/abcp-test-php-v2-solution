<?php

namespace NW\WebService\References\Operations\Notification\Operations;

abstract class AbstractOperation
{
    public function __construct(
        private array $request,
    ) {}

    abstract public function doOperation(): array;

    protected function getDataFromRequest(string $key): mixed
    {
        /** 
         * Здесь может быть много подводных камней, используя глобальную переменную $_REQUEST, которая хранит в себе и $_COOKIE, $_GET и $_POST данные.
         * Начиная с того, что через нее можно передать sql-инъекции, заканчивая тем, что мы хотим ограничить область видимости работы контейнера
         */

        return $this->request[$key] ?? null;
    }
}