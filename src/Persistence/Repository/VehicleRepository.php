<?php

namespace Persistence\Repository;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\VehicleId;
use Domain\ValueObject\VehicleType;
use PDO;

class VehicleRepository implements VehicleRepositoryInterface
{
    private PDO $pdo;

    public function __construct(\App\PostgresConnection $connection)
    {
        $this->pdo = $connection->connect();
    }

    public function getList(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM vehicles');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToVehicle'], $rows);
    }

    public function getById($id): ?Vehicle
    {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToVehicle($row);
    }

    public function deleteById($id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM vehicles WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function persist(Vehicle $vehicle): void
    {
        $exists = $this->getById($vehicle->getId()->getValue()) !== null;

        if ($exists) {
            $sql = 'UPDATE vehicles SET 
                        registration_number = :registration_number,
                        brand = :brand,
                        model = :model,
                        type = :type,
                        updated_at = :updated_at
                    WHERE id = :id';
        } else {
            $sql = 'INSERT INTO vehicles 
                        (id, registration_number, brand, model, type, created_at, updated_at) 
                    VALUES 
                        (:id, :registration_number, :brand, :model, :type, :created_at, :updated_at)';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $vehicle->getId()->getValue(),
            'registration_number' => $vehicle->getRegistrationNumber(),
            'brand' => $vehicle->getBrand(),
            'model' => $vehicle->getModel(),
            'type' => $vehicle->getType()->getValue(),
            'created_at' => $vehicle->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vehicle->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    private function mapRowToVehicle(array $row): Vehicle
    {
        return new Vehicle(
            new VehicleId($row['id']),
            $row['registration_number'],
            $row['brand'],
            $row['model'],
            new VehicleType($row['type']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
