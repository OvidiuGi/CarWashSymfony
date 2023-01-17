<?php

namespace App\Controller;

use App\Dto\AppointmentDto;
use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Repository\CarwashRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/appointments')]
class AppointmentController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;

    private CarwashRepository $carwashRepository;

    private ServiceRepository $serviceRepository;

    private UserRepository $userRepository;

    private ValidatorInterface $validator;

    public function __construct(
        AppointmentRepository $appointmentRepository,
        ValidatorInterface $validator,
        CarwashRepository $carwashRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository
    ){
        $this->appointmentRepository = $appointmentRepository;
        $this->validator = $validator;
        $this->carwashRepository = $carwashRepository;
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'appointment_getall', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        try {
            return new JsonResponse([
                'appointments' =>
                    array_map(fn($appointment) =>$appointment->jsonSerialize() ,$this->appointmentRepository->findAll())
            ], Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(path: '/{id}', name: 'appointment_get_by_id', methods: ['GET'])]
    public function getAppointmentById(int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($appointment, Response::HTTP_OK);
    }

    #[Route(name: 'appointment_add', methods: ['POST'])]
    public function addAppointment(AppointmentDto $appointmentDto): JsonResponse
    {
        try {
            $appointment = Appointment::createFromDto($appointmentDto);
            $appointment->setCarwash($this->carwashRepository->findOneBy(['name' => $appointmentDto->carwashName]));
            $appointment->setCustomer($this->userRepository->findOneBy(['email' => $appointmentDto->customerEmail]));
            $appointment->setService($this->
                                        serviceRepository->
                                        findOneBy(['description' => $appointmentDto->serviceDescription])
            );

            $errors = $this->validator->validate($appointment);
            if(\count($errors) > 0) {
                $errorArray = [];
                foreach ($errors as $error) {
                    /*
                     * @var ConstraintViolation $error
                     */
                    $errorArray[$error->getPropertyPath()] = $error->getMessage();
                }

                throw new \Exception(json_encode($errorArray));
            }

            $this->appointmentRepository->add($appointment);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Appointment registered',
            'appointment' => AppointmentDto::createFromAppointment($appointment),
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', name: 'appointment_delete', methods: ['DELETE'])]
    public function deleteAppointment(int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $this->appointmentRepository->remove($appointment);

        return new JsonResponse(['message' => 'Appointment deleted'], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', name: 'appointment_update', methods: ['PATCH'])]
    public function updateAppointment(int $id, AppointmentDto $appointmentDto): JsonResponse
    {
        try {
            $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);

            $appointment->updateFromDto($appointmentDto);

            if ($appointmentDto->carwashName !== '') {
                $appointment->setCarwash($this->carwashRepository->findOneBy(['name' => $appointmentDto->carwashName]));
            }

            if ($appointmentDto->customerEmail !== '') {
                $appointment->setCustomer($this->userRepository->findOneBy(['email' => $appointmentDto->customerEmail]));
            }

            if ($appointmentDto->serviceDescription !== '') {
                $appointment->setService($this->
                serviceRepository->
                findOneBy(['description' => $appointmentDto->serviceDescription])
                );
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $errors = $this->validator->validate($appointment);
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

        $this->appointmentRepository->add($appointment);

        return new JsonResponse([
            'message' => 'Appointment updated',
            'appointment' => AppointmentDto::createFromAppointment($appointment),
        ], Response::HTTP_OK);
    }

    #[Route(path: '/customer/{id}', name: 'appointment_by_customer', methods: ['GET'])]
    public function getByUserId(int $id): JsonResponse
    {
        try {
            $appointments = $this->appointmentRepository->findBy(['customer' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($appointments, Response::HTTP_OK);
    }
}