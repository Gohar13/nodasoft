<?php

namespace NW\WebService\References\DTO;

class OperationParamsDTO
{
    private ?int $resellerId;
    private int $clientId;
    private int $expertId;
    private int $creatorId;
    private string $notificationType;
    private ?int $differencesFrom;
    private ?int $differencesTo;
    private ?int $complaintId;
    private ?string $complaintNumber;
    private ?int $consumptionId;
    private ?string $consumptionNumber;
    private ?string $agreementNumber;
    private ?string $date;

    private function __construct(
        int $resellerId,
        string $notificationType,
        int $clientId,
        int $expertId,
        int $creatorId,
        int $differencesFrom,
        int $differencesTo,
        int $complaintId,
        int $complaintNumber,
        int $consumptionId,
        int $consumptionNumber,
        int $agreementNumber,
        int $date
    ) {
        $this->resellerId = $resellerId;
        $this->notificationType = $notificationType;
        $this->clientId = $clientId;
        $this->creatorId = $creatorId;
        $this->expertId = $expertId;
        $this->differencesFrom = $differencesFrom;
        $this->differencesTo = $differencesTo;
        $this->complaintId = $complaintId;
        $this->complaintNumber = $complaintNumber;
        $this->consumptionId = $consumptionId;
        $this->consumptionNumber = $consumptionNumber;
        $this->agreementNumber = $agreementNumber;
        $this->date = $date;
    }
    public static function create(
        int $resellerId,
        string $notificationType,
        int $clientId,
        int $expertId,
        int $creatorId,
        int $differencesFrom,
        int $differencesTo,
        int $complaintId,
        int $complaintNumber,
        int $consumptionId,
        int $consumptionNumber,
        int $agreementNumber,
        int $date
    ): self {
        return new self(
            $resellerId,
            $notificationType,
            $clientId,
            $expertId,
            $creatorId,
            $differencesFrom,
            $differencesTo,
            $complaintId,
            $complaintNumber,
            $consumptionId,
            $consumptionNumber,
            $agreementNumber,
            $date
        );
    }

    public function getResellerId(): ?int
    {
        return $this->resellerId;
    }
    public function getNotificationType(): ?int
    {
        return $this->notificationType;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function getExpertId(): ?int
    {
        return $this->expertId;
    }

    public function getDifferencesTo(): ?int
    {
        return $this->differencesTo;
    }

    public function getDifferencesFrom(): ?int
    {
        return $this->differencesFrom;
    }

    public function getComplaintId(): ?int
    {
        return $this->complaintId;
    }

    public function getComplaintNumber(): ?string
    {
        return $this->complaintNumber;
    }

    public function getConsumptionId(): ?int
    {
        return $this->consumptionId;
    }

    public function getConsumptionNumber(): ?string
    {
        return $this->consumptionNumber;
    }
    public function getAgreementNumber(): ?string
    {
        return $this->agreementNumber;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

}
