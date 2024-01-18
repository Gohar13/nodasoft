<?php

namespace NW\WebService\References\Operations;


use NW\WebService\References\DTO\OperationParamsDTO;

abstract class Operation
{
    abstract public function doOperation(): array;

    public function getDtoFromRequest($data): OperationParamsDTO
    {
        $requestData = $_REQUEST[$data];
        return  OperationParamsDTO::create(
            (int)$requestData['resellerId'],
            (int)$requestData['notificationType'],
            (int)$requestData['clientId'],
            (int)$requestData['expertId'],
            (int)$requestData['creatorId'],
            (int)$requestData['differences']['from'],
            (int)$requestData['differences']['to'],
            (int)$requestData['complaintId'],
            (string)$requestData['complaintNumber'],
            (int)$requestData['consumptionId'],
            (string)$requestData['consumptionNumber'],
            (string)$requestData['agreementNumber'],
            (string)$requestData['date'],
        );
    }
}