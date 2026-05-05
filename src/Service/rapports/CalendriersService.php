<?php

namespace App\Service\rapports;

use App\Dto\utils\OrderCriteria;
use App\Entity\rapports\Calendriers;
use App\Entity\rapports\TypeCalendriers;
use App\Repository\rapports\CalendriersRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\rapports\CalendrierDto;
use App\Service\rapports\TypeCalendriersService;

class CalendriersService extends BaseService
{
    private CalendriersRepository $repository;
    private TypeCalendriersService $typeCalendriersService;

    public function __construct(
        CalendriersRepository $repository,
        EntityManagerInterface $em,
        TypeCalendriersService $typeCalendriersService
    ) {
        $this->repository = $repository;
        $this->typeCalendriersService = $typeCalendriersService;
        parent::__construct($em);
    }
    protected function getRepository(): CalendriersRepository
    {
        return $this->repository;
    }

    /**
     * Récupérer tous les calendriers actifs
     */
    
    public function getAll(?OrderCriteria $criteria = null): array
    {
        return $this->repository->findAllActive($criteria ?? new OrderCriteria());
    }

    /**
     * Récupérer par type
     */
    public function getByType(TypeCalendriers $type, OrderCriteria $criteria): array
    {
        return $this->repository->findByType($type, $criteria);
    }

    /**
     * Récupérer entre deux dates
     */
    public function getBetweenDates(
        \DateTimeInterface $debut,
        \DateTimeInterface $fin,
        OrderCriteria $criteria
    ): array {
        return $this->repository->findBetweenDates($debut, $fin, $criteria);
    }

    /**
     * Récupérer par ID
     */
    public function getById(int $id): ?Calendriers
    {
        return $this->repository->findActiveById($id);
    }
    public function getVerifierById(int $id): Calendriers
    {
        $calendrier = $this->getById($id);
        if (!$calendrier) {
            throw new \InvalidArgumentException("Calendrier non trouvé.");
        }
        return $calendrier;
    }

    /**
     * Créer un calendrier
     */
    public function insert(
        \DateTimeInterface $dateDebut,
        \DateTimeInterface $dateFin,
        TypeCalendriers $type
    ): Calendriers {
        $calendrier = new Calendriers();
        if ($dateDebut > $dateFin) {
            throw new \InvalidArgumentException("La date de début doit être antérieure ou egal à la date de fin.");
        }   
        $calendrier->setDateDebut($dateDebut);
        $calendrier->setDateFin($dateFin);
        $calendrier->setTypeCalendriers($type);

        $this->em->persist($calendrier);
        $this->em->flush();

        return $calendrier;
    }
    public function toArrayList(array $calendriers,array $exclude = []): array
    {
        $result = [];

        foreach ($calendriers as $index => $calendrier) {
            $result[$index] = $calendrier->toArray($exclude);
        }

        return $result;
    }
    public function insertDto(CalendrierDto $dto): Calendriers
    {
        $calendrier = $dto->toEntity();
        $typeCalendrier = $this->typeCalendriersService->getById($dto->getTypeCalendrierId());
        if (!$typeCalendrier) {
            throw new \Exception('Type de calendrier non trouvé pour id ' . $dto->getTypeCalendrierId());
        }
        $calendrier->setTypeCalendriers($typeCalendrier);
        $this->em->persist($calendrier);
        $this->em->flush();
        return $calendrier;
    }
    public function getDate(
        \DateTimeInterface $date,
        OrderCriteria $criteria,
        int $heure = 0
    ): array {
        return $this->repository->findDate($date, $criteria, $heure);
    }
    public function updateCalendrier(Calendriers $calendrier,CalendrierDto $dto ): Calendriers
    {
        $calendrier->setDateDebut($dto->getDateDebut());
        $calendrier->setDateFin($dto->getDateFin());
        $typeCalendrier = $this->typeCalendriersService->getById($dto->getTypeCalendrierId());
        if (!$typeCalendrier) {
            throw new \Exception('Type de calendrier non trouvé pour id ' . $dto->getTypeCalendrierId());
        }
        $calendrier->setTypeCalendriers($typeCalendrier);
        $this->em->persist($calendrier);
        $this->em->flush();
        return $calendrier;
    }
    public function updateCalendrierDto(int $idCalendrier , CalendrierDto $calendrierDto): Calendriers
    {
        $calendrier = $this->getById($idCalendrier);
        if (!$calendrier) {
            throw new \Exception('Calendrier non trouvé pour id ' . $idCalendrier);
        }
        return $this->updateCalendrier($calendrier, $calendrierDto);
    }
    public function deleted(int $idCalendrier): void
    {
        $calendrier = $this->getById($idCalendrier);
        if (!$calendrier) {
            throw new \Exception("Calendrier non trouvé pour id $idCalendrier");
        }
        $calendrier->setDeletedAt(new \DateTimeImmutable());
        $this->em->persist($calendrier);
        $this->em->flush();
    }
    public function getBetweenDatesDebut(
        \DateTimeInterface $debut,
        \DateTimeInterface $fin,
        OrderCriteria $criteria
    ): array {
        return $this->repository->findBetweenDatesDebut($debut, $fin, $criteria);
    }


    
}