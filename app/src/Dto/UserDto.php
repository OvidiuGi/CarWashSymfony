<?php

namespace App\Dto;

use App\Entity\User;

class UserDto
{
    public int $id;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $password = '';

    public string $telephoneNr = '';

    public array $roles = [];

    public static function createFromUser(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->roles[] = $user->roles;
        $dto->lastName = $user->lastName;
        $dto->firstName = $user->firstName;
        $dto->email = $user->email;
        $dto->password = $user->password;
        $dto->telephoneNr = $user->telephoneNr;

        return $dto;
    }


}