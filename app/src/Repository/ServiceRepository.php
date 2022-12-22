<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, Service::class);
    }

    public function add(Service $entity, bool $flush = true): void
    {
        if (!$this->findOneBy(['id' => $entity->getId()])) {
            $this->entityManager->persist($entity);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function update(Service $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Service $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findAll(): array
    {
        $services = parent::findAll();
        foreach ($services as $service) {
            $response[] = $service;
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?Service
    {
        $service = parent::findOneBy($criteria, $orderBy);
        if (!$service) {
            throw new \Exception('Service not found');
        }

        return $service;
    }
}