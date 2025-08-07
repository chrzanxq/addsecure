<?php

namespace App\Container;

use App\PostgresConnection;
use Domain\Service\VehiclesBuilder;
use Domain\Service\VehiclesReader;
use Domain\Service\VehiclesWriter;
use Persistence\Repository\VehicleRepository;

class Container
{
    private array $instances = [];

    public function get(string $class)
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = $this->resolve($class);
        }
        return $this->instances[$class];
    }

    private function resolve(string $class)
    {
        return match ($class) {
            PostgresConnection::class => new PostgresConnection(),

            VehicleRepository::class => new VehicleRepository(
                $this->get(PostgresConnection::class)
            ),

            VehiclesBuilder::class => new VehiclesBuilder($this->get(VehicleRepository::class)),
            VehiclesReader::class => new VehiclesReader($this->get(VehicleRepository::class)),
            VehiclesWriter::class => new VehiclesWriter($this->get(VehicleRepository::class)),

            default => throw new \RuntimeException("Cannot resolve class: $class"),
        };
    }
}
