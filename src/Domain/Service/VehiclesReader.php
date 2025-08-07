<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Vehicle;
use Domain\Service\VehicleDTO;

class VehiclesReader
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getVehicleById($id): ?VehicleDTO
    {
        $vehicle = $this->vehicleRepository->getById($id);

        if (!$vehicle instanceof Vehicle) {
            return null;
        }

        $dto = new VehicleDTO();
        $dto->id = $vehicle->getId();
        $dto->registrationNumber = $vehicle->getRegistrationNumber();
        $dto->brand = $vehicle->getBrand();
        $dto->model = $vehicle->getModel();
        $dto->type = $vehicle->getType();
        $dto->createdAt = $vehicle->getCreatedAt();
        $dto->updatedAt = $vehicle->getUpdatedAt();

        return $dto;
    }
}
