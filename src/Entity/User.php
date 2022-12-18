<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: '`user`')]
class User
{
    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_CLIENT = 'ROLE_CLIENT';

    public const ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_TRAINER'];

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email()]
    public string $email = '';

    #[ORM\Column(type: 'json')]
    #[Assert\Choice(choices: User::ROLES, multiple: true)]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/')]
    public string $password = '';

    #[ORM\Column(type: 'string', unique: 'true')]
    public string $telephoneNr = '';

    #[ORM\Column(type: 'string', unique: 'true')]
    #[Assert\NotBlank()]
    #[Assert\Regex("/^[A-Z][a-z]+$/")]
    public string $firstName = '';

    #[ORM\Column(type: 'string', unique: 'true')]
    #[Assert\NotBlank()]
    #[Assert\Regex("/^[A-Z][a-z]+$/")]
    public string $lastName = '';

    /**
     * Many Users have Many Appointments.
     */
    #[ORM\ManyToMany(targetEntity: 'Appointment', mappedBy: 'users')]
    private Collection $appointments;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_CUSTOMER
        $roles[] = self::ROLE_CUSTOMER;

        return \array_values(\array_unique($roles));
    }

    public function setRoles(array $roles): self
    {
        if (!\in_array($roles[0], self::ROLES)) {
            throw new UnexpectedValueException($roles, 'The role does not exist!');
        }

        $this->roles = \array_values(\array_unique($roles));

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointments $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->addUser($this);
        }

        return $this;
    }

    public function removeAppointment(Appointments $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            $appointment->removeUser($this);
        }

        return $this;
    }

    public function setAppointments(Collection $appointments): self
    {
        $this->appointments = $appointments;

        return $this;
    }


}