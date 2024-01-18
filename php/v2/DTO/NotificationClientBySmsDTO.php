<?php

namespace NW\WebService\References\DTO;

class NotificationClientBySmsDTO
{
    private bool $isSent = false;
    private string $message = '';

    public static function create(): self
    {
        return new self();
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }
    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): NotificationClientBySmsDTO
    {
        $this->message = $message;

        return $this;
    }

    public function setIsSent(bool $sent): NotificationClientBySmsDTO
    {
        $this->isSent = $sent;

        return $this;
    }
}
