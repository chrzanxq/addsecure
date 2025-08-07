<?php

namespace Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class VehicleId
{
    private string $uuid;

    public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
    }

    public function getValue(): string
    {
        return $this->uuid;
    }

        public function __toString(): string
    {
        return $this->uuid; 
    }

}
