<?php

namespace App\Entity;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: '`user`')]
class User implements \JsonSerializable, UserInterface, PasswordAuthenticatedUserInterface
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
    private array $roles;

    #[ORM\Column(type: 'string')]
    public string $password = '';

    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/')]
    public string $plainPassword = '';

    #[ORM\Column(type: 'string', unique: 'true')]
    #[Assert\NotBlank()]
    #[Assert\Regex(pattern: '/^(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/')]
    public string $telephoneNr = '';

    #[ORM\Column(type: 'string', unique: 'false')]
    #[Assert\NotBlank()]
    #[Assert\Regex("/^[A-Z][a-z]+$/")]
    public string $firstName = '';

    #[ORM\Column(type: 'string', unique: 'false')]
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
        $user->plainPassword = $userDto->password;
        $user->telephoneNr = $userDto->telephoneNr;
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->roles = $userDto->roles;

        return $user;
    }

    public function updateFromDto(UserDto $userDto): self
    {

        $this->email = $userDto->email == '' ? $this->email : $userDto->email;
        $this->password = $userDto->password == '' ? $this->password : $userDto->password;
        $this->telephoneNr = $userDto->telephoneNr == '' ? $this->telephoneNr : $userDto->telephoneNr;
        $this->firstName = $userDto->firstName == '' ? $this->firstName : $userDto->firstName;
        $this->lastName = $userDto->lastName == '' ? $this->lastName : $userDto->lastName;
        $this->roles = $userDto->roles == [] ? $this->roles : $userDto->roles;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->roles,
            'telephoneNr' => $this->telephoneNr,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}