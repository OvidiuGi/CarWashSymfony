<?php

namespace App\Dto;

use App\Entity\Carwash;

class CarwashDto
{
    public int $id;

    public string $name = '';

    public string $address = '';

    public string $ownerEmail = '';

    public array $serviceId = [];

    public static function createFromCarwash(Carwash $carwash): self
    {
        $dto = new self();
        $dto->id = $carwash->getId();
        $dto->name = $carwash->name;
        $dto->address = $carwash->address;
        $dto->ownerEmail = $carwash->getOwner()->email;
        foreach ($carwash->getServices() as $service) {
            $dto->serviceId[] = $service->getId();
        }

        return $dto;
    }
}