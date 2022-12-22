<?php

namespace App\Entity;

use App\Dto\CarwashDto;
use App\Repository\CarwashRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: CarwashRepository::class)]
#[Table(name: '`carwash`')]
class Carwash implements \JsonSerializable
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    #[Assert\NotBlank()]
    public string $address = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    #[Assert\NotBlank()]
    public string $name = '';

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id')]
    private User $owner;

    #[ORM\ManyToMany(targetEntity: 'Service', inversedBy: 'carwashes')]
    #[ORM\JoinTable(name: 'carwash_service')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addCarwash($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            return $this;
        }

        $this->services->removeElement($service);
        $service->removeCarwash($this);

        return $this;
    }

    public function setServices(Collection $services): self
    {
        $this->services = $services;

        return $this;
    }

    public static function createFromDto(CarwashDto $dto): self
    {
        $carwash = new self();
        $carwash->address = $dto->address;
        $carwash->name = $dto->name;
        return $carwash;
    }

    public function updateFromDto(CarwashDto $carwashDto): self
    {

        $this->name = $carwashDto->name == '' ? $this->name : $carwashDto->name;
        $this->address = $carwashDto->address == '' ? $this->address : $carwashDto->address;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'name' => $this->name,
            'owner' => $this->owner,
            'services' => $this->services->getValues(),
        ];
    }
}