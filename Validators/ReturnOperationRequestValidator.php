<?php

namespace NW\WebService\References\Operations\Notification\Data\Validators;
use NW\WebService\References\Operations\Notification\Data\Enums\OperationStatus;
use NW\WebService\References\Operations\Notification\Data\Enums\ReturnOperationStatus;
use NW\WebService\References\Operations\Notification\Exceptions\ReturnOperationRequestException as RequestException;

class ReturnOperationRequestValidator
{
    public function validate(array $requestData)
    {
        if (empty((int) $requestData['resellerId'])) {
            throw new RequestException('Empty resellerId');
        }

        if (empty((int) $requestData['clientId'])) {
            throw new RequestException('Empty clientId');
        }

        if (empty((int) $requestData['creatorId'])) {
            throw new RequestException('Empty creatorId');
        }

        if (empty((int) $requestData['expertId'])) {
            throw new RequestException('Empty expertId');
        }

        $notificationType = (int) $requestData['notificationType'];
        if (empty($notificationType)) {
            throw new RequestException('Empty notificationType');
        }

        if (!ReturnOperationStatus::tryFrom($notificationType)) {
            throw new RequestException('Incorrect notificationType');
        }

        if ((int) $requestData['notificationType'] === ReturnOperationStatus::TYPE_CHANGE
            && empty($requestData['differences'])
            ) {
            throw new RequestException('NotificationType is equal to TYPE_CHANGE, but differences is empty');
        }

        if (!empty($requestData['differences'])) {
            if (!OperationStatus::tryFrom($requestData['differences']['to'])) {
                throw new RequestException('Differences status changing \'to\' invalid');
            }

            if (!OperationStatus::tryFrom($requestData['differences']['from'])) {
                throw new RequestException('Differences status changing \'from\' invalid');
            }
            // можно еще добавить проверок на то, что статус нельзя изменить с одного на другой, или вообще нельзя изменить, такие как, например COMPLETED.
        }
    }
}