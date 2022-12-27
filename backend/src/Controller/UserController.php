<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route(path: '/users')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(name: 'user_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse([
            'users' => array_map(fn($user) => $user->jsonSerialize() ,$this->userRepository->findAll())
            ], Response::HTTP_OK
        );
    }

    #[Route(name: 'user_add', methods: ['POST'])]
    public function addUser(UserDto $userDto): JsonResponse
    {
        $user = User::createFromDto($userDto);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->plainPassword));

        $errors = $this->validator->validate($user);
        if(\count($errors) > 0) {
            $errorArray = [];
            foreach ($errors as $error) {
                /*
                 * @var ConstraintViolation $error
                 */
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['message' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        try{
            $this->userRepository->add($user);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'User registered',
            'user' => UserDto::createFromUser($user),
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', name: 'user_get_by_id', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['user' => UserDto::createFromUser($user)], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $this->userRepository->remove($user);

        return new JsonResponse([
            'message' => 'User deleted',
        ], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'user_update', methods: ['PATCH'])]
    public function updateUser(int $id, UserDto $userDto): JsonResponse
    {
        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $user->updateFromDto($userDto);

        $errors = $this->validator->validate($user);
        if(\count($errors) > 0) {
            $errorArray = [];
            foreach ($errors as $error) {
                /*
                 * @var ConstraintViolation $error
                 */
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($errorArray, Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->add($user);

        return new JsonResponse([
            'message' => 'User updated',
            'user' => UserDto::createFromUser($user),
        ], Response::HTTP_OK);
    }
}