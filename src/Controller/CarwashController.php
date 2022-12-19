<?php

namespace App\Controller;

use App\Dto\CarwashDto;
use App\Entity\Carwash;
use App\Repository\CarwashRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/carwashes')]
class CarwashController extends AbstractController
{
    private CarwashRepository $carwashRepository;

    private ValidatorInterface $validator;

    private ServiceRepository $serviceRepository;

    private UserRepository $userRepository;

    public function __construct(
        CarwashRepository $carwashRepository,
        ValidatorInterface $validator,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository
    ) {
        $this->carwashRepository = $carwashRepository;
        $this->validator = $validator;
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'carwash_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->carwashRepository->findAll(),Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'carwash_get_by_id', methods: ['GET'])]
    public function getCarwashById(int $id): JsonResponse
    {
        $carwash = $this->carwashRepository->find($id)->jsonSerialize();

        if($carwash === null) {
            return new JsonResponse(['message' => 'Carwash not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($carwash, Response::HTTP_OK);
    }

    #[Route(name: 'carwash_add', methods: ['POST'])]
    public function addCarwash(CarwashDto $carwashDto): JsonResponse
    {
        $carwash = Carwash::createFromDto($carwashDto);
        foreach ($carwashDto->serviceId as $serviceId) {
            $service = $this->serviceRepository->find($serviceId);
            if($service === null) {
                return new JsonResponse(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
            $carwash->addService($service);
        }

        $carwash->setOwner($this->userRepository->findOneBy(['email' => $carwashDto->ownerEmail]));

        $errors = $this->validator->validate($carwash);

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

        $this->carwashRepository->add($carwash);

        return new JsonResponse([
            'message' => 'Carwash added',
            'user' => CarwashDto::createFromCarwash($carwash),
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', name: 'carwash_delete', methods: ['DELETE'])]
    public function deleteCarwash(int $id): JsonResponse
    {
        $carwash = $this->carwashRepository->find($id);
        if($carwash === null) {
            return new JsonResponse(['message' => 'Carwash not found'], Response::HTTP_NOT_FOUND);
        }

        $this->carwashRepository->remove($carwash);

        return new JsonResponse(['message' => 'Carwash deleted'], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'carwash_update', methods: ['PATCH'])]
    public function updateCarwash(int $id, CarwashDto $carwashDto): JsonResponse
    {
        $carwash = $this->carwashRepository->find($id);

        if($carwash === null) {
            return new JsonResponse(['message' => 'Carwash not found'], Response::HTTP_NOT_FOUND);
        }
        $carwash->updateFromDto($carwashDto);

        if ($carwashDto->ownerEmail !== $carwash->getOwner()->email && $carwashDto->ownerEmail !== '') {
            $carwash->setOwner($this->userRepository->findOneBy(['email' => $carwashDto->ownerEmail]));
        }

        $errors = $this->validator->validate($carwash);

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

        $this->carwashRepository->add($carwash);

        return new JsonResponse([
            'message' => 'Carwash updated',
            'user' => CarwashDto::createFromCarwash($carwash),
        ], Response::HTTP_OK);
    }
}