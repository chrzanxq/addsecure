<?php 

namespace App\Validator;

use Domain\Exception\ValidationException;

class SaveVehicleValidator
{
    public function validate(array $data): array
    {
        $registrationNumber = strtoupper(trim($data['registrationNumber'] ?? ''));
        $brand = trim($data['brand'] ?? '');
        $model = trim($data['model'] ?? '');
        $type = $data['type'] ?? '';

        if (empty($registrationNumber)) {
            throw new ValidationException('Registration number is required.');
        }

        if (empty($brand)) {
            throw new ValidationException('Brand is required.');
        }

        if (empty($model)) {
            throw new ValidationException('Model is required.');
        }

        if (!in_array($type, ['passenger', 'bus', 'truck'], true)) {
            throw new ValidationException('Invalid vehicle type.');
        }

        return [
            'registrationNumber' => $registrationNumber,
            'brand' => $brand,
            'model' => $model,
            'type' => $type,
        ];
    }
}
