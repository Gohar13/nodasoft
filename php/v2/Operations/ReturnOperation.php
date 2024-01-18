<?php

namespace NW\WebService\References\Operations;

use NW\WebService\References\DTO\OperationParamsDTO;
use NW\WebService\References\DTO\OperationResultDTO;
use NW\WebService\References\Events\NotificationEvent;
use NW\WebService\References\Exception\InvalidParameterException;
use NW\WebService\References\Exception\InvalidTemplateDataException;
use NW\WebService\References\Exception\NotFoundException;
use NW\WebService\References\Models\Contractor;
use NW\WebService\References\Models\Employee;
use NW\WebService\References\Models\Seller;
use NW\WebService\References\Models\Status;

class ReturnOperation extends Operation
{
    private Contractor $creator;
    private Contractor $expert;
    private Contractor $client;
    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;

    const EMAIL_FROM = 'contractor@example.com'; // Need to be in config file, or env

    const EMAIL_TO_LIST = [
        'someemeil@example.com',
        'someemeil2@example.com'
    ];

    /**
     * @throws \Exception
     */
    public function doOperation(): array
    {
        $operationParamsDTO = $this->getDtoFromRequest('data');
        $notificationType = $operationParamsDTO->getNotificationType();

        if (!$notificationType) {
            throw new InvalidParameterException('Empty notificationType', 422);
        }

        $operationResultDTO = OperationResultDTO::create();

        if (!$operationParamsDTO->getResellerId()) {
            $operationResultDTO->setNotificationClientBySmsMessage('Empty resellerId');

            return $operationResultDTO->toArray();
        }

        $this->validateContractors($operationParamsDTO);

        $templateData = $this->getTemplateData($operationParamsDTO, $this->creator, $this->expert, $this->client);

        if ($this->shouldSendEmail()) {
            $emailsToSendData = [];
            foreach (self::EMAIL_TO_LIST as $email) {
                $emailsToSendData[] = $this->getEmailData(self::EMAIL_FROM, $email, $templateData, $operationParamsDTO->getResellerId());
            }

            MessagesClient::sendMessage($emailsToSendData, $operationParamsDTO->getResellerId(), NotificationEvent::CHANGE_RETURN_STATUS);
            $operationResultDTO->setNotificationEmployeeByEmail(true);
        }

        if ($notificationType === self::TYPE_CHANGE && $operationParamsDTO->getDifferencesTo()) {
            if ($this->shouldSendClientEmail($this->client)) {
                MessagesClient::sendMessage(
                    [
                        [
                            $this->getEmailData(self::EMAIL_FROM, $this->client->getEmail(), $templateData, $operationParamsDTO->getResellerId())
                        ],
                    ],
                    $operationParamsDTO->getResellerId(),
                    $this->client->getId(),
                    NotificationEvent::CHANGE_RETURN_STATUS,
                    $operationParamsDTO->getDifferencesTo()
                );
                $operationResultDTO->setNotificationClientByEmail(true);
            }

            if (!empty($this->client->getMobile())) {
                if (NotificationManager::send($operationParamsDTO->getResellerId(), $this->client->getId(), NotificationEvent::CHANGE_RETURN_STATUS, $operationParamsDTO->getDifferencesTo(), $templateData)) {
                    $operationResultDTO->setNotificationClientBySmsIsSent(true);
                }
            }
        }

        return $operationResultDTO->toArray();
    }

    private function shouldSendEmail(): bool
    {
        return self::EMAIL_FROM && count(self::EMAIL_TO_LIST) > 0;
    }

    private function shouldSendClientEmail(Contractor $client): bool
    {
        return self::EMAIL_FROM && $client->getEmail();
    }

    private function getDifferences(OperationParamsDTO $operationParamsDTO)
    {
        if ($operationParamsDTO->getNotificationType() === self::TYPE_NEW) {
            return __('NewPositionAdded', null, $operationParamsDTO->getResellerId());
        } elseif ($operationParamsDTO->getNotificationType() === self::TYPE_CHANGE) {
            return __('PositionStatusHasChanged', [
                'FROM' => Status::getName($operationParamsDTO->getDifferencesFrom()),
                'TO'   => Status::getName($operationParamsDTO->getDifferencesTo()),
            ], $operationParamsDTO->getResellerId());
        }

        return null;
    }

    /**
     * @throws InvalidTemplateDataException
     */
    private function getTemplateData(
        OperationParamsDTO $operationParamsDTO,
        Contractor $creator,
        Contractor $expert,
        Contractor $client
    ): array
    {
        $templateData = [
            'COMPLAINT_ID'       => $operationParamsDTO->getComplaintId(),
            'COMPLAINT_NUMBER'   => $operationParamsDTO->getComplaintNumber(),
            'CREATOR_ID'         => $operationParamsDTO->getCreatorId(),
            'CREATOR_NAME'       => $creator->getFullName(),
            'EXPERT_ID'          => $operationParamsDTO->getExpertId(),
            'EXPERT_NAME'        => $expert->getFullName(),
            'CLIENT_ID'          => $operationParamsDTO->getClientId(),
            'CLIENT_NAME'        => $client->getFullName(),
            'CONSUMPTION_ID'     => $operationParamsDTO->getConsumptionId(),
            'CONSUMPTION_NUMBER' => $operationParamsDTO->getConsumptionNumber(),
            'AGREEMENT_NUMBER'   => $operationParamsDTO->getAgreementNumber(),
            'DATE'               => $operationParamsDTO->getDate(),
            'DIFFERENCES'        => $this->getDifferences($operationParamsDTO),
        ];

        $this->validateTemplateData($templateData);

        return $templateData;
    }

    /**
     * @throws InvalidTemplateDataException
     */
    private function validateTemplateData(array $templateData)
    {
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new InvalidTemplateDataException(sprintf("Template Data (%s) is empty!", $key), 422);
            }
        }
    }

    private function getEmailData(string $emailFrom, string $emailTo, array $templateData, int $resellerId): array
    {
        return [
            'emailFrom' => $emailFrom,
            'emailTo'   => $emailTo,
            'subject'   => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
            'message'   => __('complaintEmployeeEmailBody', $templateData, $resellerId),
        ];
    }

    /**
     * @throws NotFoundException
     */
    private function validateContractors($operationParamsDTO)
    {
        $reseller = Seller::getById($operationParamsDTO->getResellerId());
        if (!$reseller) {
            throw new NotFoundException('Seller not found!', 404);
        }
        
        $client = Contractor::getById($operationParamsDTO->getClientId());
        if (!$client || $client->getType() !== Contractor::TYPE_CUSTOMER || $client->getSeller()->getId() !== $operationParamsDTO->getResellerId()) {
            throw new NotFoundException('Client not found!', 404);
        }

        $creator = Employee::getById($operationParamsDTO->getCreatorId());
        if (!$creator) {
            throw new NotFoundException('Creator not found!', 404);
        }

        $expert = Employee::getById($operationParamsDTO->getExpertId());
        if (!$expert) {
            throw new NotFoundException('Expert not found!', 404);
        }

        $this->client = $client;
        $this->creator = $creator;
        $this->expert = $expert;
    }
}
