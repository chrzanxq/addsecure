<?php

namespace Domain\Entity;

use Domain\ValueObject\VehicleType;

class Vehicle
{
    private ?string $id;
    private string $registrationNumber;
    private string $brand;
    private string $model;
    private VehicleType $type;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        ?string $id,
        string $registrationNumber,
        string $brand,
        string $model,
        VehicleType $type,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->registrationNumber = strtoupper($registrationNumber);
        $this->brand = $brand;
        $this->model = $model;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?string
    {
        return $this->id ? $this->id : null;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }


    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getType(): VehicleType
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(string $brand, string $model, VehicleType $type): void
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->type = $type;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
