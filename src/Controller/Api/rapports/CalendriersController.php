<?php

namespace App\Controller\Api\rapports;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\rapports\CalendrierDto;
use App\Dto\utils\OrderCriteria;
use App\Service\rapports\CalendriersService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Service\rapports\CalendriersUtilisateursService;
#[Route('/calendriers')]
class CalendriersController extends BaseApiController
{
    private CalendriersService $service;
    private CalendriersUtilisateursService $calendriersUtilisateursService;

    public function __construct(CalendriersService $service, CalendriersUtilisateursService $calendriersUtilisateursService)
    {
        $this->service = $service;
        $this->calendriersUtilisateursService = $calendriersUtilisateursService;
    }
    #[Route('', name: 'api_calendriers_create', methods: ['POST'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function createCalendrier(Request $request): JsonResponse
    {
        
        try {
            $dto = $this->deserializeAndValidate(
                $request,
                CalendrierDto::class
            );
            
            $rapportInsertArray = $this->service->insertDto($dto);
            return $this->jsonSuccess($rapportInsertArray);

        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		}        
    }
    #[Route('', name: 'api_get_calendriers', methods: ['GET'])]
    #[TokenRequired]
    public function getCalendriers(Request $request): JsonResponse
    {
        try {
            // 1. Extraction des variables brutes du request et du .env
            $rawDateDebut = $request->query->get('dateDebut');
            $rawDateFin = $request->query->get('dateFin');
            $monthOffset = $_ENV['CALENDAR_MONTH_OFFSET'] ?? 2;

            // 2. Traitement logique avec opérateurs ternaires
            $dateDebut = $rawDateDebut 
                ? new \DateTime($rawDateDebut) 
                : (new \DateTime())->modify("-$monthOffset months")->setTime(0, 0, 0);

            $dateFin = $rawDateFin 
                ? new \DateTime($rawDateFin) 
                : (new \DateTime())->setTime(23, 59, 59);

            // 3. Appel au service
            $listeCalendriers = $this->service->getBetweenDatesDebut($dateDebut, $dateFin, new OrderCriteria());
            
            $exclude = ['createdAt', 'deletedAt'];
            $listeCalendriersArray = $this->service->toArrayList($listeCalendriers, $exclude);
            
            return $this->jsonSuccess($listeCalendriersArray);  
            
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        } 
    }
    #[Route('/utilisateur', name: 'api_get_calendriers_utilisateur_disponible', methods: ['GET'])]
    #[TokenRequired]
    public function getCalendriersUtilisateurDisponible(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            
            
            $date = new \DateTime();

            $criteria = new OrderCriteria();

            $listeCalendriers = $this->calendriersUtilisateursService
                ->getCalendrierDisponibleDate($user, $date, $criteria);

            $exclude = ['createdAt','deletedAt'];
            $listeCalendriersArray = $this->service->toArrayList($listeCalendriers, $exclude);

            return $this->jsonSuccess($listeCalendriersArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/{id}', name: 'api_update_calendrier', methods: ['PUT'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function updateCalendrier(Request $request, int $id): JsonResponse
    {
        try {   
            $dto = $this->deserializeAndValidate(
                $request,
                CalendrierDto::class
            );
            $calendrier = $this->service->updateCalendrierDto($id, $dto);
            return $this->jsonSuccess($calendrier->toArray());
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/{id}', name: 'api_get_calendrier', methods: ['GET'] ,requirements: ['id' => '\d+'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function getCalendrier(int $id): JsonResponse
    {
        try {   
                
            $calendrier = $this->service->getVerifierById($id);
            $excludes = ['createdAt','deletedAt'];
            return $this->jsonSuccess($calendrier->toArray($excludes));
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    
    #[Route('/{id}', name: 'api_delete_calendrier', methods: ['DELETE'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function deleteCalendrier(int $id): JsonResponse
    {
        try {
            $this->service->deleted($id);

            return $this->jsonSuccess('Calendrier supprimé avec succès', 200);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/supervision', name: 'api_get_calendriers_supervision', methods: ['GET'])]
    #[TokenRequired]
    public function getCalendrierSupervision(Request $request): JsonResponse
    {
        try {
            $dateString = $request->query->get('date');
            $date = $dateString ? new \DateTime($dateString) : new \DateTime();

            $criteria = new OrderCriteria();

            $listeCalendriers = $this->service->getDate($date, $criteria);
            $exclude = ['createdAt','deletedAt'];
            $listeCalendriersArray = $this->service->toArrayList($listeCalendriers, $exclude);

            return $this->jsonSuccess($listeCalendriersArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }



}