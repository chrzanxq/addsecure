<?php

namespace Domain\Service;

use Domain\Exception\ValidationException;

class VehicleDTO
{
    public $id;
    public $registrationNumber;
    public $brand;
    public $model;
    public $type;
    public $createdAt;
    public $updatedAt;


    public function validate(): void
    {
        $errors = [];

        if (empty($this->registrationNumber)) {
            $errors[] = 'Registration number is required.';
        }

        if (empty($this->brand)) {
            $errors[] = 'Brand is required.';
        }

        if (empty($this->model)) {
            $errors[] = 'Model is required.';
        }

        if (!in_array($this->type, ['passenger', 'bus', 'truck'])) {
            $errors[] = 'Invalid vehicle type.';
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(' ', $errors));
        }
    }

}
