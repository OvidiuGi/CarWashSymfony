<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/users')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'user_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->userRepository->findAll(),Response::HTTP_OK);
    }

    #[Route(name: 'user_register', methods: ['POST'])]
    public function register(UserDto $userDto): JsonResponse
    {
        $user = User::createFromDto($userDto);
        $this->userRepository->save($user);

        return new JsonResponse([
            'message' => 'User registered',
            'user' => UserDto::createFromUser($user),
        ], Response::HTTP_CREATED);
    }
}