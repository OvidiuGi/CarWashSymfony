<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;

#[Entity(repositoryClass: AppointmentRepository::class)]
#[Table(name: '`appointment`')]
class Appointment
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startTime;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endTime;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private ?User $customer;

    #[ORM\ManyToOne(targetEntity: Carwash::class)]
    #[ORM\JoinColumn(name: 'carwash_id', referencedColumnName: 'id')]
    private ?Carwash $carwash;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id')]
    private ?Service $service;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCarwash(): ?Carwash
    {
        return $this->carwash;
    }

    public function setCarwash(?Carwash $carwash): self
    {
        $this->carwash = $carwash;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }
}