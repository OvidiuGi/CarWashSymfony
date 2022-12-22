<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, User::class);
    }

    public function add(User $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findAll(): array
    {
        $users = parent::findAll();
        foreach ($users as $user) {
            $response[] = $user;
        }

        return $response;
    }


    /**
     * @throws \Exception
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?User
    {
        $user = parent::findOneBy($criteria, $orderBy);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }
}
