<?php

namespace NW\WebService\References\Operations\Notification\Data\Validators;

use NW\WebService\References\Operations\Notification\Data\Enums\ContractorType;
use NW\WebService\References\Operations\Notification\Data\Transporters\Inputs\OperationInput;
use NW\WebService\References\Operations\Notification\Exceptions\OperationInputException;

class ReturnOperationInputValidator
{
    public function validate(OperationInput $input)
    {
        if ($input->reseller === null) {
            throw new OperationInputException('Seller not found!');
        }

        if ($input->client === null) {
            throw new OperationInputException('Client not found!');
        }

        if ($input->client->type !== ContractorType::TYPE_CUSTOMER) {
            throw new OperationInputException('Client is not a customer!');
        }
        // $client->Seller->id !== $resellerId - не понимаю зачем эта проверка, если мы ищем reseller`а по resellerId

        if ($input->creator === null) {
            throw new OperationInputException('Creator not found!');
        }

        if ($input->expert === null) {
            throw new OperationInputException('Expert not found!');
        }
    }
}