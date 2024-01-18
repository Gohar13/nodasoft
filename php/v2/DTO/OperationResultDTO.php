<?php

namespace NW\WebService\References\DTO;

class OperationResultDTO
{
    private bool $notificationEmployeeByEmail = false;
    private bool $notificationClientByEmail = false;
    private NotificationClientBySmsDTO $notificationClientBySms;

    private function __construct()
    {
      $this->notificationClientBySms = NotificationClientBySmsDTO::create();
    }
    public static function create(): self {
        return new self();
    }

    public function setNotificationClientBySmsMessage($message): OperationResultDTO
    {
        $this->notificationClientBySms->setMessage($message);

        return $this;
    }

    public function setNotificationClientBySmsIsSent(bool $sent): OperationResultDTO
    {
        $this->notificationClientBySms->setIsSent($sent);

        return $this;
    }

    public function setNotificationEmployeeByEmail(bool $notificationEmployeeByEmail): OperationResultDTO
    {
        $this->notificationEmployeeByEmail = $notificationEmployeeByEmail;

        return $this;
    }

    public function setNotificationClientByEmail(bool $notificationClientByEmail): OperationResultDTO
    {
        $this->notificationClientByEmail = $notificationClientByEmail;

        return $this;
    }

    public function toArray(): array
    {
       return [
            'notificationEmployeeByEmail' => $this->notificationEmployeeByEmail,
            'notificationClientByEmail'   => $this->notificationClientByEmail,
            'notificationClientBySms'     => [
                'isSent'  => $this->notificationClientBySms->isSent(),
                'message' => $this->notificationClientBySms->getMessage(),
            ],
        ];
    }
}
