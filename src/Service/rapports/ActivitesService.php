<?php
namespace App\Service\rapports;

use App\Repository\rapports\ActivitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\rapports\CalendriersUtilisateurs;
use App\Service\rapports\EffectsImpactsService;
use App\Entity\rapports\Activites;
use App\Dto\utils\OrderCriteria;
class ActivitesService
{
    private EntityManagerInterface $em;
    private ActivitesRepository $activitesRepository;

    private EffectsImpactsService $effectsImpactsService;
    
    public function __construct(EntityManagerInterface $em, ActivitesRepository $activitesRepository, EffectsImpactsService $effectsImpactsService)
    {
        $this->em = $em;
        $this->activitesRepository = $activitesRepository;
        $this->effectsImpactsService = $effectsImpactsService;
    }
    public function insert(Activites $activite): Activites
    {
        $this->em->persist($activite);
        $this->em->flush();
        return $activite;
    }
    public function findByCalendrierUtilisateur(CalendriersUtilisateurs $calendrierUtilisateur, string $order = 'DESC'): array
    {
        return $this->activitesRepository->findByCalendrierUtilisateur($calendrierUtilisateur, $order);
    }
    public function transformerArray(array $activites, array $exclude = []): array
    {
        $result = [];
        foreach ($activites as $index => $activite) {
            $impacts = $this->effectsImpactsService->getByActiviteTypeId($activite, 1,new OrderCriteria());
            $effects = $this->effectsImpactsService->getByActiviteTypeId($activite, 2,new OrderCriteria());
            $produits = $this->effectsImpactsService->getByActiviteTypeId($activite, 3,new OrderCriteria());
            $cibles = $this->effectsImpactsService->getByActiviteTypeId($activite, 4,new OrderCriteria());
            $previsions = $this->effectsImpactsService->getByActiviteTypeId($activite, 5,new OrderCriteria());
            $realisations = $this->effectsImpactsService->getByActiviteTypeId($activite, 6,new OrderCriteria());
            $tauxRealisations = $this->effectsImpactsService->getByActiviteTypeId($activite, 7,new OrderCriteria());
            $observations = $this->effectsImpactsService->getByActiviteTypeId($activite, 8,new OrderCriteria());
            $result[$index] ['activite'] = $activite->toArray($exclude);
            $result[$index]['impacts'] = $this->effectsImpactsService->transformerArray($impacts, $exclude);
            $result[$index]['effects'] = $this->effectsImpactsService->transformerArray($effects, $exclude);
            $result[$index]['produits'] = $this->effectsImpactsService->transformerArray($produits, $exclude);
            $result[$index]['cibles'] = $this->effectsImpactsService->transformerArray($cibles, $exclude);
            $result[$index]['previsions'] = $this->effectsImpactsService->transformerArray($previsions, $exclude);
            $result[$index]['realisations'] = $this->effectsImpactsService->transformerArray($realisations, $exclude);
            $result[$index]['taux'] = $this->effectsImpactsService->transformerArray($tauxRealisations, $exclude);
            $result[$index]['observations'] = $this->effectsImpactsService->transformerArray($observations, $exclude);
        }
        return $result;
    }
    
    
}