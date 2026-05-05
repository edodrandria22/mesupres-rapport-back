<?php

namespace App\Service\rapports;

use App\Entity\rapports\TypeCalendriers;
use App\Repository\rapports\TypeCalendriersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class TypeCalendriersService
{
    private TypeCalendriersRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(
        TypeCalendriersRepository $repository,
        EntityManagerInterface $em
    ) {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function getAllActive(string $order = 'ASC'): array
    {
        return $this->repository->findAllActive($order);
    }

    public function getById(int $id): ?TypeCalendriers
    {
        return $this->repository->findActiveById($id);
    }

    public function getByName(string $name): ?TypeCalendriers
    {
        return $this->repository->findOneByName($name);
    }

}