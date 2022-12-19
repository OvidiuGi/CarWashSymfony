<?php

namespace App\Entity;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: '`user`')]
class User
{
    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_CARWASH_OWNER = 'ROLE_CARWASH_OWNER';

    public const ROLES = [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_CARWASH_OWNER];

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email()]
    public string $email = '';

    #[ORM\Column(type: 'json')]
    #[Assert\Choice(choices: User::ROLES, multiple: true)]
    public array $roles = [];

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

    public function getId(): int
    {
        return $this->id;
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

    public static function createFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->email = $userDto->email;
        $user->password = $userDto->password;
        $user->telephoneNr = $userDto->telephoneNr;
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->roles = $userDto->roles;

        return $user;
    }
}