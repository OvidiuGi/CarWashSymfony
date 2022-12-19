<?php

namespace App\Dto;

use App\Entity\Appointment;

class AppointmentDto
{
    public int $id;

    public string $customerEmail = '';

    public string $carwashName = '';

    public string $serviceDescription = '';

    public string $startTime = '';

    public  string $endTime = '';

    public static function createFromAppointment(Appointment $appointment): self
    {
        $dto = new self();
        $dto->id = $appointment->getId();
        $dto->customerEmail = $appointment->getCustomer()->email;
        $dto->carwashName = $appointment->getCarwash()->name;
        $dto->serviceDescription = $appointment->getService()->description;
        $dto->startTime = $appointment->getStartTime()->format('Y-m-d H:i');
        $dto->endTime = $appointment->getEndTime()->format('Y-m-d H:i');

        return $dto;
    }
}