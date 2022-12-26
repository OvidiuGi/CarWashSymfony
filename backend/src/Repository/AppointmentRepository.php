<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, Appointment::class);
    }

    public function add(Appointment $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Appointment $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @throws \Exception
     */
    public function findAll(): array
    {
        $appointments = parent::findAll();
        foreach ($appointments as $appointment) {
            $response[] = $appointment;
        }


        if (empty($response)) {
            throw new \Exception('Appointments not found');
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?Appointment
    {
        $appointment = parent::findOneBy($criteria, $orderBy);
        if (!$appointment) {
            throw new \Exception('Appointment not found');
        }

        return $appointment;
    }
}
