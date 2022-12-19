<?php

namespace App\Dto;

use App\Entity\Service;

class ServiceDto
{
    public int $id;

    public int $price = 0;

    public string $description = '';

    public static function createFromService(Service $service): self
    {
        $dto = new self();
        $dto->id = $service->getId();
        $dto->price = $service->price;
        $dto->description = $service->description;

        return $dto;
    }
}