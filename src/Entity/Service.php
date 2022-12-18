<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: '`service`')]
class Service
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    public int $price = 0;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
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
}