<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function index(Request $request): Response
    {
        try {
            $user = $this->userRepository->findOneBy(['email' => $request->toArray()['email']]);
        } catch (\Exception $e) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        if($this->passwordHasher->isPasswordValid($user, $request->toArray()['password'])) {
            return new JsonResponse(['status' => 'success', 'role' => $user->getRoles()[0]], Response::HTTP_OK);
        }

        return $this->json([
            'status' => 'failed',
            'role' => '',
        ]);
    }
}