<?php

namespace App\Controller;

use App\Container\Container;
use App\Validator\SaveVehicleValidator;
use Domain\Service\VehiclesBuilder;
use Domain\Service\VehiclesReader;
use Domain\Service\VehiclesWriter;
use Domain\Service\VehicleDTO;
use Domain\Exception\ValidationException;
use Domain\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class VehicleController extends BaseController
{
 private VehiclesBuilder $builder;
    private VehiclesReader $reader;
    private VehiclesWriter $writer;

    public function __construct()
    {
        $container = new Container();
        $this->builder = $container->get(VehiclesBuilder::class);
        $this->reader = $container->get(VehiclesReader::class);
        $this->writer = $container->get(VehiclesWriter::class);
    }

    public function index(): Response
    {
        ob_start();
        include __DIR__ . '/../views/index.php';
        return new Response(ob_get_clean());
    }

    public function list(): JsonResponse
    {
        $results = $this->builder->getList();
        return $this->toJsonResponse(['results' => $results]);
    }


    public function save(?string $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $validated = (new SaveVehicleValidator())->validate($data);

            $dto = new VehicleDTO();
            $dto->id =  $id;
            $dto->registrationNumber = $validated['registrationNumber'];
            $dto->brand = $validated['brand'];
            $dto->model = $validated['model'];
            $dto->type = $validated['type'];

            $this->writer->saveVehicle($dto);

            return $this->toJsonResponse([]);

        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }




    public function delete(string $id): JsonResponse
    {
        try {
            $vehicle = $this->reader->getVehicleById($id);
            if (!$vehicle) {
                throw new EntityNotFoundException("Vehicle with ID $id not found.");
            }

            $this->writer->deleteById($id);
            //zwykle 200
            return $this->toJsonResponse(['status' => 'deleted', 'id' => $id]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
