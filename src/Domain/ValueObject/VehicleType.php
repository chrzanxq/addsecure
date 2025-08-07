<?php

namespace Domain\ValueObject;

class VehicleType
{
    public const PASSENGER = 'passenger';
    public const BUS = 'bus';
    public const TRUCK = 'truck';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::all())) {
            throw new \InvalidArgumentException("Invalid vehicle type: $value");
        }

        $this->value = $value;
    }

    public static function all(): array
    {
        return [self::PASSENGER, self::BUS, self::TRUCK];
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
