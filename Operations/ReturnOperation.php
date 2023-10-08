<?php

namespace NW\WebService\References\Operations\Notification\Operations;

use NW\WebService\References\Operations\Notification\Data\Enums\NotificationEvents;
use NW\WebService\References\Operations\Notification\Data\Enums\NotificationType;
use NW\WebService\References\Operations\Notification\Data\Enums\OperationStatus;
use NW\WebService\References\Operations\Notification\Data\Enums\ReturnOperationStatus;
use NW\WebService\References\Operations\Notification\Data\Models\Client;
use NW\WebService\References\Operations\Notification\Data\Models\Complaint;
use NW\WebService\References\Operations\Notification\Data\Models\Consumption;
use NW\WebService\References\Operations\Notification\Data\Models\Employee;
use NW\WebService\References\Operations\Notification\Data\Models\Reseller;
use NW\WebService\References\Operations\Notification\Data\Repositories\ClientRepository;
use NW\WebService\References\Operations\Notification\Data\Repositories\EmployeeRepository;
use NW\WebService\References\Operations\Notification\Data\Repositories\ResellerRepository;
use NW\WebService\References\Operations\Notification\Data\Transporters\DTO\Differences;
use NW\WebService\References\Operations\Notification\Data\Transporters\Inputs\OperationInput;
use NW\WebService\References\Operations\Notification\Data\Transporters\Outputs\OperationError;
use NW\WebService\References\Operations\Notification\Data\Transporters\Outputs\OperationOutput;
use NW\WebService\References\Operations\Notification\Data\Validators\ReturnOperationInputValidator as InputValidator;
use NW\WebService\References\Operations\Notification\Data\Validators\ReturnOperationRequestValidator as RequestValidator;
use NW\WebService\References\Operations\Notification\Exceptions\OperationInputException;
use NW\WebService\References\Operations\Notification\Exceptions\ReturnOperationRequestException as RequestException;
use NW\WebService\References\Operations\Notification\Exceptions\SendingMailException;

class ReturnOperation extends AbstractOperation
{
    public function doOperation(): OperationOutput|OperationError
    {
        try {
            $outputSuccess = new OperationOutput;

            $requestData = $this->getDataFromRequest($this->requestDataKey());
            // сначала мы валидируем данные, которые находятся в запросе
            $this->validateRequest($requestData);

            // собираем все данные в один объект, в которых хранится всё необходимое для выполнения операции
            $operationInput = $this->buildOperationInputFromArray($requestData);
            // валидируем объект, собранный из данных запроса
            $this->validateOperationInput($operationInput);
            // я разделил валидатор на два отдельных, потому что данные запроса мы можем возвращать с определенным статусом, что улучшает работу с ошибками, с которыми обычно приходят клиенты в тех.поддержку
            // так же у каждого валидатора своя область ответственности. Валидаторы не собирают данные, а только проверяют данные массива, или объекта в случае 'OperationInput'

            // я не совсем понял, почему в примере кода мы хотим отправлять ошибки клиенту по смс
            // ведь в случае ошибки выполнение операции должно быть прервано
            // мне кажется более правильным вариантов будет вывести ошибку об операции в окне вызова, возвращая его из API
            $this->sendMails($operationInput, $outputSuccess);

            return $outputSuccess;
        } catch (OperationInputException|RequestException $e) {
            $outputError = new OperationError($e->getMessage(), $e->getCode());

            return $outputError;
        }
    }

    private function sendMails(OperationInput $input, OperationOutput $output): OperationOutput
    {
        try {
            $templateData = [
                'COMPLAINT_ID'          => $input->complaint->id,
                'COMPLAINT_NUMBER'      => $input->complaint->number,
                'CREATOR_ID'            => $input->creator->id,
                'CREATOR_NAME'          => $input->creator->getFullName(),
                'EXPERT_ID'             => $input->expert->id,
                'EXPERT_NAME'           => $input->expert->getFullName(),
                'CLIENT_ID'             => $input->client->id,
                'CLIENT_NAME'           =>$input->client->getFullName(),
                'CONSUMPTION_ID'        => $input->consumption->id,
                'CONSUMPTION_NUMBER'    => $input->consumption->number,
                'AGREEMENT_NUMBER'      => $input->agreementNumber,
                'DATE'                  => $input->date,
                'DIFFERENCES'           => $this->getOperationDifferences($input),
            ];

            foreach ($templateData as $key => $tempData) {
                if (empty($tmpData)) {
                    $message = sprintf('Template Data: %s: empty', $key);

                    throw new SendingMailException($message);
                }
            }
            
            $emails = Settings::getEmailsByPermit($input->reseller->id, 'tsGoodsReturn');
            if (!empty($input->reseller->email) && count($emails) > 0) {
                foreach($emails as $email) {
                    MessageClient::sendMessage(
                        data: [0 => [
                            'emailFrom' => $input->reseller->email,
                            'emailTo'   => $email,
                            'subject'   => ResellerMailRenderer::render('complaintEmployeeEmailSubject', $templateData, $input->reseller->id),
                            'message'   => ResellerMailRenderer::render('complaintEmployeeEmailBody', $templateData, $input->reseller->id)
                        ]],
                        resellerId: $input->reseller->id,
                        notificationEvents: NotificationEvents::CHANGE_RETURN_STATUS,
                    );
                }
                $output->notificationEmployeeByEmail = true;
            }

            if ($input->notificationType !== ReturnOperationStatus::TYPE_CHANGE || empty($input->differences->to)) {
                return $output;
            }

            if (!empty($input->reseller->email) && !empty($input->client->email)) {
                MessageClient::sendMessage(
                    data: [0 => [
                        'emailFrom' => $input->reseller->email,
                        'emailTo'   => $email,
                        'subject'   => ResellerMailRenderer::render('complaintClientEmailSubject', $templateData, $input->reseller->id),
                        'message'   => ResellerMailRenderer::render('complaintClientEmailBody', $templateData, $input->reseller->id)
                    ]],
                    resellerId: $input->reseller->id,
                    notificationEvents: NotificationEvents::CHANGE_RETURN_STATUS,
                    operationStatus: OperationStatus::from($input->differences->to),
                );
                $output->notificationClientByEmail = true;
            }

            if (!empty($input->client->mobileNotifications)) {
                // в 
                $res = NotificationManager::send(
                    resellerId: $input->reseller->id,
                    clientId: $input->client->id,
                    notificationEvents: NotificationEvents::CHANGE_RETURN_STATUS,
                    operationStatus: OperationStatus::from($input->differences->to),
                    templateData: $templateData
                );

                $output->notificationClientBySms = !!$res;
            }

        } catch (SendingMailException $e) {
            // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
            // но прерываем ли операцию? не знаю...
        }

        return $output;
    }

    private function getOperationDifferences(OperationInput $input)
    {
        $differences = "";
        // в ReturnOperationRequestValidator есть валидация типа уведомления, который должен быть одним из кейсов enum ReturnOperationStatus
        if ($input->notificationType === ReturnOperationStatus::TYPE_NEW) {
            $differences = ResellerMailRenderer::render(
                'NewPositionAdded',
                null,
                $input->reseller->id
            );
        } else {
            $differences = ResellerMailRenderer::render(
                'PositionStatusHasChanged',
                [
                    "FROM" => OperationStatus::from($input->differences->from),
                    "TO" => OperationStatus::from($input->differences->to),
                ],
                $input->reseller->id
            );
        }

        return $differences;
    }

    private function buildOperationInputFromArray(array $a): OperationInput
    {
        $input = new OperationInput;
        
        $input->reseller    = $this->getReseller((int) $a['resellerId']);
        $input->client      = $this->getClient((int) $a['clientId']);
        $input->creator     = $this->getEmployee((int) $a['creatorId']);
        $input->expert      = $this->getEmployee((int) $a['expertId']);
        
        $input->notificationType    = NotificationType::from((int) $a['notificationType']);

        $input->differences         = new Differences;
        $input->differences->to     = (int) $a['differences']['to'] ?? null;
        $input->differences->from   = (int) $a['differences']['from'] ?? null;

        $input->consumption         = new Consumption;
        $input->consumption->id     = (int) $a['consumptionId'];
        $input->consumption->number = (string) $a['consumptionNumber'];
        
        $input->complaint           = new Complaint;
        $input->complaint->id       = (int) $a['complaintId'];
        $input->complaint->number   = (string) $a['complaintNumber'];

        $input->agreementNumber     = $a['agreementNumber'];
        $input->date                = $a['date'];

        return $input;
    }

    /**
     * по хорошему конечно, чтобы репозитории были в DI-контейнере...
     */
    private function getReseller(int $id): Reseller|null
    {
        return (new ResellerRepository)->getById($id);
    }

    private function getClient(int $id): Client|null
    {
        return (new ClientRepository)->getById($id);
    }

    private function getEmployee(int $id): Employee|null
    {
        return (new EmployeeRepository)->getById($id);
    }

    private function validateOperationInput(OperationInput $input)
    {
        $validator = new InputValidator;
        $validator->validate($input);
    }

    private function validateRequest(array $requestData)
    {
        $validator = new RequestValidator;
        $validator->validate($requestData);
    }

    private function requestDataKey()
    {
        return 'data';
    }
}

/** ниже фиктивные классы */
class ResellerMailRenderer
{
    public static function render(
        string $template,
        ?array $options,
        int $resellerId,
    ) {
        // some text generating
        return "rendered something";
    }
}

class MessageClient
{
    public static function sendMessage(
        array $data,
        int $resellerId,
        NotificationEvents $notificationEvents,
        ?OperationStatus $operationStatus = null,
        ?array $options = null,
        ?int $clientId = null,
    ) {}
}

class Settings
{
    public static function getEmailsByPermit($resellerId, $event): array
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}

class NotificationManager
{
    public static function send(
        int $resellerId,
        int $clientId,
        NotificationEvents $notificationEvents,
        OperationStatus $operationStatus = null,
        array $templateData,
        ?string $error = null,
    )
    {
        // )
        return false ?? true;
    }
}