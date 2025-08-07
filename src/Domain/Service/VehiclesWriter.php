<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleType;

class VehiclesWriter
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function saveVehicle(VehicleDTO $vehicleDTO): void
    {
        $id = $vehicleDTO->id ?? null;

        $vehicle = new Vehicle(
            $id ?? $id ?: null,
            $vehicleDTO->registrationNumber,
            $vehicleDTO->brand,
            $vehicleDTO->model,
            new VehicleType($vehicleDTO->type),
            $vehicleDTO->createdAt ?? new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->vehicleRepository->persist($vehicle);
    }


    public function deleteById($id): void
    {
        $this->vehicleRepository->deleteById($id);
    }
}
