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

#[Route(path: '/users')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private ValidatorInterface $validator;

    public function __construct(UserRepository $userRepository, ValidatorInterface $validator)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    #[Route(name: 'user_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->userRepository->findAll(),Response::HTTP_OK);
    }

    #[Route(name: 'user_add', methods: ['POST'])]
    public function addUser(UserDto $userDto): JsonResponse
    {
        $user = User::createFromDto($userDto);

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
            'message' => 'User registered',
            'user' => UserDto::createFromUser($user),
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', name: 'user_get_by_id', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id)->jsonSerialize();

        if(!$user) {
            return new JsonResponse([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(UserDto::createFromUser($user), Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if(!$user) {
            return new JsonResponse([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->userRepository->remove($user);

        return new JsonResponse([
            'message' => 'User deleted',
        ], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'user_update', methods: ['PATCH'])]
    public function updateUser(int $id, UserDto $userDto): JsonResponse
    {
        $user = $this->userRepository->find(['id' => $id]);

        if(!$user) {
            return new JsonResponse([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
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