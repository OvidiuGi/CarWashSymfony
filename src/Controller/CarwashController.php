<?php

namespace App\Controller;

use App\Repository\CarwashRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/carwash')]
class CarwashController extends AbstractController
{
    private CarwashRepository $carwashRepository;

    public function __construct(CarwashRepository $carwashRepository)
    {
        $this->carwashRepository = $carwashRepository;
    }

    #[Route(name: 'carwash_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->carwashRepository->findAll(),Response::HTTP_OK);
    }
}