<?php

namespace App\Entity;

use App\Dto\ServiceDto;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: '`service`')]
class Service implements \JsonSerializable
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank()]
    public int $price = 0;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    #[Assert\NotBlank()]
    public string $description = '';

    #[ORM\ManyToMany(targetEntity: 'Carwash', mappedBy: 'services')]
    private Collection $carwashes;

    public function __construct()
    {
        $this->carwashes = new ArrayCollection();
    }

    public function getCarwashes(): Collection
    {
        return $this->carwashes;
    }

    public function addCarwash(Carwash $carwash): self
    {
        if (!$this->carwashes->contains($carwash)) {
            $this->carwashes->add($carwash);
            $carwash->addService($this);
        }

        return $this;
    }

    public function removeCarwash(Carwash $carwash): self
    {
        if (!$this->carwashes->contains($carwash)) {
            return $this;
        }

        $this->carwashes->removeElement($carwash);
        $carwash->removeService($this);

        return $this;
    }

    public function setCarwash(Collection $carwash): self
    {
        $this->carwashes = $carwash;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function createFromDto(ServiceDto $dto): self
    {
        $service = new self();
        $service->price = $dto->price;
        $service->description = $dto->description;

        return $service;
    }

    public function updateFromDto(ServiceDto $dto): self
    {
        $this->price = $dto->price == 0 ? $this->price : $dto->price;
        $this->description = $dto->description == '' ? $this->description : $dto->description;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'description' => $this->description,
        ];
    }
}