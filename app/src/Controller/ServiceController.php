<?php

namespace App\Controller;

use App\Dto\ServiceDto;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/services')]
class ServiceController extends AbstractController
{
    private ServiceRepository $serviceRepository;

    private ValidatorInterface $validator;

    public function __construct(
        ServiceRepository $serviceRepository,
        ValidatorInterface $validator
    ){
        $this->serviceRepository = $serviceRepository;
        $this->validator = $validator;
    }

    #[Route(name: 'service_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse([
            'services' => array_map(fn($service) => $service->jsonSerialize() ,$this->serviceRepository->findAll())
        ], Response::HTTP_OK
        );
    }

    #[Route(path: '/{id}', name: 'service_get_by_id', methods: ['GET'])]
    public function getServiceById(int $id): JsonResponse
    {
        try {
            $service = $this->serviceRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($service->jsonSerialize(), Response::HTTP_OK);
    }

    #[Route(name: 'service_add', methods: ['POST'])]
    public function addService(ServiceDto $dto): JsonResponse
    {
        $service = Service::createFromDto($dto);

        $errors = $this->validator->validate($service);
        if (count($errors) > 0) {
            $errorArray = [];
            foreach ($errors as $error) {
                /*
                 * @var ConstraintViolation $error
                 */
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['message' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->serviceRepository->add($service);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Service added',
            'service' => ServiceDto::createFromService($service)
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', name: 'service_delete', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        try {
            $service = $this->serviceRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $this->serviceRepository->remove($service);

        return new JsonResponse(['message' => 'Service deleted'], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'service_update', methods: ['PATCH'])]
    public function updateService(int $id, ServiceDto $dto): JsonResponse
    {
        try {
            $service = $this->serviceRepository->findOneBy(['id' => $id]);

            $service->updateFromDto($dto);

            $errors = $this->validator->validate($service);
            if (count($errors) > 0) {
                $errorArray = [];
                foreach ($errors as $error) {
                    /*
                     * @var ConstraintViolation $error
                     */
                    $errorArray[$error->getPropertyPath()] = $error->getMessage();
                }

                throw new \Exception(json_encode($errorArray));
            }

            $this->serviceRepository->update($service);

        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'message' => 'Service updated',
            'service' => ServiceDto::createFromService($service)
        ], Response::HTTP_OK);
    }
}