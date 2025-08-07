<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;
use Domain\ValueObject\VehicleType;

class VehiclesWriter
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function saveVehicle(VehicleDTO $vehicleDTO): int
    {
        $id = $vehicleDTO->id ?? null;

        $vehicle = new Vehicle(
            $id !== null ? new VehicleId($id) : null,
            $vehicleDTO->registrationNumber,
            $vehicleDTO->brand,
            $vehicleDTO->model,
            new VehicleType($vehicleDTO->type),
            $vehicleDTO->createdAt ?? new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        return $this->vehicleRepository->persist($vehicle);
    }


    public function deleteById($id)
    {
        $this->vehicleRepository->deleteById($id);
    }
}
