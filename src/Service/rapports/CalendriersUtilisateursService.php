<?php

namespace App\Service\rapports;

use App\Dto\rapports\ActiviteCollectionDto;
use App\Entity\rapports\CalendriersUtilisateurs;
use App\Entity\rapports\Calendriers;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\rapports\CalendriersUtilisateursRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\rapports\Activites;
use Exception;
use App\Dto\utils\OrderCriteria;
use App\Service\utilisateurs\UtilisateursService;

class CalendriersUtilisateursService
{
    private CalendriersUtilisateursRepository $repository;
    private EntityManagerInterface $em;
    private ActivitesService $activitesService;
    private CalendriersService $calendriersService;
    private EffectsImpactsService $effectsImpactsService;
    private UtilisateursService $utilisateursService;
    public function __construct(
        CalendriersUtilisateursRepository $repository,
        EntityManagerInterface $em,
        ActivitesService $activitesService,
        CalendriersService $calendriersService,
        EffectsImpactsService $effectsImpactsService,
        UtilisateursService $utilisateursService
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->activitesService = $activitesService;
        $this->calendriersService = $calendriersService;
        $this->effectsImpactsService = $effectsImpactsService;
        $this->utilisateursService = $utilisateursService;
    }

    /**
     * Assigner un utilisateur à un calendrier
     */
    public function insert(Utilisateurs $utilisateur, Calendriers $calendrier): CalendriersUtilisateurs
    {
        $existing = $this->repository->findOneByUtilisateurAndCalendrier($utilisateur, $calendrier);
        if ($existing) {
            throw new \InvalidArgumentException("L'utilisateur est déjà assigné à ce calendrier.");
        }

        $cu = new CalendriersUtilisateurs();
        $cu->setUtilisateur($utilisateur);
        $cu->setCalendrier($calendrier);

        $this->em->persist($cu);
        $this->em->flush();

        return $cu;
    }
    public function delete(CalendriersUtilisateurs $cu): CalendriersUtilisateurs
    {
        $cu->setDeletedAt(new \DateTimeImmutable());
        $this->em->persist($cu);
        $this->em->flush();
        return $cu;
    }
    public function getByCalendrierAndUtilisateur(Utilisateurs $utilisateur, Calendriers $calendrier): ?CalendriersUtilisateurs
    {
        return $this->repository->findOneByUtilisateurAndCalendrier($utilisateur, $calendrier);
    }

    public function getByUtilisateur(Utilisateurs $utilisateur, string $order = 'DESC',int $limit = 10): array
    {
        return $this->repository->findByUtilisateur($utilisateur, $order, $limit);
    }

    public function getByCalendrier(Calendriers $calendrier, string $order = 'DESC'): array
    {
        return $this->repository->findByCalendrier($calendrier, $order);
    }
    public function toArray(CalendriersUtilisateurs $calendrierUtilisateur,array $exclude = []): array
    {
        $activites = $this->activitesService->findByCalendrierUtilisateur($calendrierUtilisateur);
        $excludeRapport = array_diff($exclude, ['deletedAt']);
        $result = $calendrierUtilisateur->toArray($excludeRapport); 
        $excludeActivite = $exclude;
        $excludeActivite[] = 'id';
        $result['activites'] = $this->activitesService->transformerArray($activites, $excludeActivite);
        return $result;
    }
    public function transformerArray(array $calendrierUtilisateurs): array
    {
        $result = [];
        $exclude = ['deletedAt', 'createdAt'];
        foreach ($calendrierUtilisateurs as $index => $calendrierUtilisateur) {
            $result[$index] = $this->toArray($calendrierUtilisateur, $exclude);
        }
        return $result;
    }
    /**
     * Insert une liste d'effets d'impact
     */
    private function insertListeEffectImpactDto(array $effectImpactDtos,Activites $activite,int $typeEffectImpactId): void
    {
        foreach ($effectImpactDtos as $effectImpactDto) {
            $effectImpactClass = $effectImpactDto->getEffectsImpactsClass();
            $effectImpactClass->setActivite($activite);
            $this->effectsImpactsService->insertTypeId($effectImpactClass, $typeEffectImpactId);
        }
    }
    public function validerTaux(array $previsions, array $realisations): void
    {
        if (empty($previsions) || empty($realisations)) {
            throw new Exception("Les prévisions et réalisations doivent contenir au moins un élément.");
        }

        $previsionObj = $previsions[0];
        $realisationObj = $realisations[0];

        if (
            !$previsionObj->getEffectsImpactsClass() ||
            !$realisationObj->getEffectsImpactsClass()
        ) {
            throw new Exception("Les objets EffectsImpacts sont invalides.");
        }

        $prevision = $previsionObj->getEffectsImpactsClass()->getName();
        $realisation = $realisationObj->getEffectsImpactsClass()->getName();

        if ($prevision === null || $realisation === null) {
            throw new Exception("Valeur de prévision ou réalisation manquante.");
        }

        // 👉 Exemple de logique métier (à adapter)
        if ($prevision == 0) {
            throw new Exception("La prévision ne peut pas être 0 pour calculer le taux.");
        }
    }
    public function insertRapportDto(Utilisateurs $utilisateur, ActiviteCollectionDto $activiteCollectionDto): CalendriersUtilisateurs
    {
        $this->em->beginTransaction();

        try {
            $calendrier = $this->calendriersService->getById($activiteCollectionDto->getIdCalendrier());
            if (!$calendrier) {
                throw new Exception("Le calendrier n'existe pas pour id=" . $activiteCollectionDto->getIdCalendrier());
            }
            $typeCalendrierId = $calendrier->getTypeCalendriers()->getId();
            $calendrierUtilisateur = $this->insert($utilisateur, $calendrier);

            foreach ($activiteCollectionDto->getActivites() as $activiteDto) {
                $activite = $activiteDto->getActiviteClass();
                $activite->setCalendrierUtilisateur($calendrierUtilisateur);
                $activite = $this->activitesService->insert($activite);
                
                #Pour insert liste impact et effet
                $this->insertListeEffectImpactDto($activiteDto->getImpacts(), $activite, 1);
                $this->insertListeEffectImpactDto($activiteDto->getEffects(), $activite, 2);
                #Pour connaitre que c'est trimestre
                if ($typeCalendrierId==3 || $typeCalendrierId==4) {
                    $this->validerTaux(
                        $activiteDto->getPrevisions(),
                        $activiteDto->getRealisations()
                    );
                    $this->insertListeEffectImpactDto($activiteDto->getProduits(), $activite, 3);
                    $this->insertListeEffectImpactDto($activiteDto->getCibles(), $activite, 4);
                    $this->insertListeEffectImpactDto($activiteDto->getPrevisions(), $activite, 5);
                    $this->insertListeEffectImpactDto($activiteDto->getRealisations(), $activite, 6);
                    $this->insertListeEffectImpactDto($activiteDto->getTaux(), $activite, 7);
                    $this->insertListeEffectImpactDto($activiteDto->getObservations(), $activite, 8);
                }
            }

            $this->em->commit();

            return $calendrierUtilisateur;

        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
    public function getByCalendrierId(int $idCalendrier,string $order = 'DESC'): array
    {
        $calendrier = $this->calendriersService->getById($idCalendrier);
        if (!$calendrier) {
            throw new Exception("Le calendrier n'existe pas pour id=" . $idCalendrier);
        }
        return $this->getByCalendrier($calendrier, $order);
    }
    /**
     * Récupérer tous les calendriers disponibles pour un utilisateur si le calendrier n'est pas dans la liste des calendriers utilisateur
     */
    public function getAllCalendrierDisponible(Utilisateurs $utilisateurs,array $listeCalendriers):array
    {
        $result = array();
        foreach($listeCalendriers as $calendrier)
        {
            $calendrierUtilisateur = $this->getByCalendrierAndUtilisateur($utilisateurs, $calendrier);
            if (!$calendrierUtilisateur) {
                $result[] = $calendrier;
            }
        }
        return $result;
    }
    public function getCalendrierDisponibleDate(Utilisateurs $utilisateur,\DateTimeInterface $date,OrderCriteria $criteria):array
    {
        $calendriers = $this->calendriersService->getDate($date, $criteria,-12);
        return $this->getAllCalendrierDisponible($utilisateur,$calendriers);
    }
    public function getById(int $idCalendrierUtilisateur): CalendriersUtilisateurs
    {
        return $this->repository->findActiveById($idCalendrierUtilisateur);
    }
    public function existeCalendrierUtilisateur(int $idCalendrierUtilisateur): CalendriersUtilisateurs
    {
        $calendrierUtilisateur = $this->getById($idCalendrierUtilisateur);
        if (!$calendrierUtilisateur) {  
            throw new Exception("Le calendrier utilisateur n'existe pas pour id=" . $idCalendrierUtilisateur);
        }
        return $calendrierUtilisateur;
    }
    public function validateCalendrierUtilsateur(int $idCalendrierUtilisateur): CalendriersUtilisateurs
    {
        $calendrierUtilisateur = $this->existeCalendrierUtilisateur($idCalendrierUtilisateur);
        $calendrierUtilisateur->setDateValidation(new \DateTimeImmutable());
        $this->em->persist($calendrierUtilisateur);
        $this->em->flush();
        return $calendrierUtilisateur;
    }
    public function changerStatusValidation(CalendriersUtilisateurs $calendrierUtilisateur): CalendriersUtilisateurs
    {
        if ($calendrierUtilisateur->getDateValidation()) {
            $calendrierUtilisateur->setDateValidation(null);
        }
        else{
            $calendrierUtilisateur->setDateValidation(new \DateTimeImmutable());
        }
        $this->em->persist($calendrierUtilisateur);
        $this->em->flush();
        return $calendrierUtilisateur;
    }
    public function changerStatusValidationId(int $idCalendrierUtilisateur): CalendriersUtilisateurs
    {
        $calendrierUtilisateur = $this->existeCalendrierUtilisateur($idCalendrierUtilisateur);
        return $this->changerStatusValidation($calendrierUtilisateur);  
    }
    public function modifierRapport(Utilisateurs $utilisateur, ActiviteCollectionDto $activiteCollectionDto,int $idCalendrierUtilisateur):CalendriersUtilisateurs
    {
        $this->em->beginTransaction();
        try {
            $calendrierUtilisateur = $this->existeCalendrierUtilisateur($idCalendrierUtilisateur);
            if ($calendrierUtilisateur->getDateValidation()) {
                throw new Exception("Calendrier deja valider pour id =".$calendrierUtilisateur->getId());
            }
            $utilisateurOvaina= $calendrierUtilisateur->getUtilisateur();
            $roleUtilsateurOvaina = $utilisateurOvaina->getRole()->getId();
            $roleUtilisateur = $utilisateur->getRole()->getId();
            $roleId = 2;// pour role utilisateur
            if ($roleUtilisateur==$roleId&&$roleUtilsateurOvaina!= $roleUtilisateur) {
                throw new Exception("Seule l'utilisateur conserner peut modifier son rapport");
            }
            $this->delete($calendrierUtilisateur);
            
            $result = $this->insertRapportDto($utilisateurOvaina,$activiteCollectionDto);
            $result->setDateValidation($calendrierUtilisateur->getDateValidation());
            $this->em->commit();
            $result = $this->getById($result->getId());
            return $result;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
        
    }
    public function getByCalendrierAndUtilisateurDeletedAt(Utilisateurs $utilisateur, Calendriers $calendrier): array
    {
        return $this->repository->findOneByUtilisateurAndCalendrierDeletedAt($utilisateur, $calendrier);
    }
    public function getByCalendrierAndUtilisateurDeletedAtId(int $idUtilisateur, int $idCalendrier): array
    {
        $utilisateur = $this->utilisateursService->getUserById($idUtilisateur);
        $calendrier = $this->calendriersService->getById($idCalendrier);
        if (!$utilisateur) {
            throw new Exception("Utilisateur non trouvé pour id=" . $idUtilisateur);
        }
        if (!$calendrier) {
            throw new Exception("Calendrier non trouvé pour id=" . $idCalendrier);
        }
        return $this->repository->findOneByUtilisateurAndCalendrierDeletedAt($utilisateur, $calendrier);
    }
    public function getAllCalendrierByDate(
        Utilisateurs $utilisateurs,
        \DateTimeInterface $date,
        OrderCriteria $criteria
    ): array {

        $calendriers = $this->calendriersService->getDate($date, $criteria);
        $result = [];

        foreach ($calendriers as $calendrier) {

            $calendrierUtilisateur = $this->getByCalendrierAndUtilisateur($utilisateurs, $calendrier);

            if ($calendrierUtilisateur === null) {
                continue; // passer au calendrier suivant
            }

            $idCalendrierUtilisateur = $calendrierUtilisateur->getId();

            $apidirina = $this->getById($idCalendrierUtilisateur);

            $result[] = $apidirina;
        }


        return $result;
    }
}