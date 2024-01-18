<?php

namespace NW\WebService\References\Models;

class Contractor
{
    const TYPE_CUSTOMER = 0;
    private int $id;
    private int $type;
    private string $name;
    private ?string $email;
    private ?string $mobile;
    private ?Seller $seller;

    public function __construct(int $id, int $type, string $name)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
    }

    public static function getById(int $resellerId): self
    {
        $type = self::TYPE_CUSTOMER;
        $name = 'Default Name';

        return new self($resellerId, $type, $name);
    }

    public function getFullName(): string
    {
        return  trim(sprintf("%s %s", $this->name, $this->id));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }
}