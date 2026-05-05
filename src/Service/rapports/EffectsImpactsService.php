<?php

namespace App\Service\rapports;

use App\Dto\utils\OrderCriteria;
use App\Entity\rapports\Activites;
use App\Repository\rapports\EffectsImpactsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\rapports\EffectsImpacts;
use App\Entity\rapports\TypeEffectImpacts;
class EffectsImpactsService
{
    private EffectsImpactsRepository $repository;
    private EntityManagerInterface $em;
    private TypeEffectImpactsService $typeEffectImpactsService;

    public function __construct(
        EffectsImpactsRepository $repository,
        EntityManagerInterface $em,
        TypeEffectImpactsService $typeEffectImpactsService
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->typeEffectImpactsService = $typeEffectImpactsService;
    }
    public function insert(EffectsImpacts $effectImpact): EffectsImpacts
    {
        $this->em->persist($effectImpact);
        $this->em->flush();
        return $effectImpact;
    }
    
    public function getByActivite(Activites $activite, OrderCriteria $criteria): array
    {
        return $this->repository->findByActivite($activite, $criteria);
    }
    public function transformerArray(array $effectsImpacts, array $exclude = []): array
    {
        $result = [];
        foreach ($effectsImpacts as $index => $effectImpact) {
            $result[$index] = $effectImpact->toArray($exclude);
        }
        return $result;
    }
    public function insertType(EffectsImpacts $effectImpact, TypeEffectImpacts $typeEffectImpact): EffectsImpacts
    {
        $effectImpact->setTypeEffectImpact($typeEffectImpact);
        return $this->insert($effectImpact);
    }
    public function insertTypeId(EffectsImpacts $effectImpact,int $idTypeEffectImpact): EffectsImpacts
    {
        $typeEffectImpact = $this->typeEffectImpactsService->getById($idTypeEffectImpact);
        if (!$typeEffectImpact) {
            throw new \InvalidArgumentException("Le type d'effet d'impact n'existe pas pour id=" . $idTypeEffectImpact);
        }
        return $this->insertType($effectImpact, $typeEffectImpact);
    }
    public function getByActiviteType(Activites $activite,TypeEffectImpacts $typeEffectImpact,OrderCriteria $criteria): array
    {
        return $this->repository->findByActiviteType($activite,$typeEffectImpact,$criteria);
    }
    public function getByActiviteTypeId(Activites $activite,int $idTypeEffectImpact,OrderCriteria $criteria): array
    {
        $typeEffectImpact = $this->typeEffectImpactsService->getById($idTypeEffectImpact);
        if (!$typeEffectImpact) {
            throw new \InvalidArgumentException("Le type d'effet d'impact n'existe pas pour id=" . $idTypeEffectImpact);
        }
        return $this->getByActiviteType($activite,$typeEffectImpact,$criteria);
    }

    
}
