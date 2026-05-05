<?php

namespace App\Controller\Api\rapports;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\rapports\ActiviteCollectionDto;
use App\Dto\rapports\MailRappelDTO;
use App\Dto\utils\BaseNomDto;
use App\Dto\utils\OrderCriteria;
use App\Entity\rapports\LI;
use App\Entity\rapports\OS;
use App\Service\rapports\CalendriersUtilisateursService;
use App\Service\rapports\LIService;
use App\Service\rapports\OSService;
use App\Service\utils\MailService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;

#[Route('/rapports')]
class RapportsController extends BaseApiController
{
    private CalendriersUtilisateursService $cus;
    private OSService $OSService;
    private LIService $LIService;
    private MailService $mailService;

    public function __construct(CalendriersUtilisateursService $calendriersUtilisateursService,OSService $OSService,LIService $LIService, MailService $mailService)
    {
        $this->cus = $calendriersUtilisateursService;
        $this->OSService = $OSService;
        $this->LIService = $LIService;
        $this->mailService = $mailService;
    }
    #[Route('', name: 'api_rapports_create', methods: ['POST'])]
    #[TokenRequired]
    public function createRapport(Request $request): JsonResponse
    {
        try {
            $dto = $this->deserializeAndValidate(
                $request,
                ActiviteCollectionDto::class
            );

            $user = $this->getUserFromRequest($request);
            
            $rapportInsert = $this->cus->insertRapportDto($user, $dto);
            $rapportInsertArray = $rapportInsert->toArray();
            return $this->jsonSuccess($rapportInsertArray);

        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		}        
    }
    #[Route('', name: 'api_rapports_get', methods: ['GET'])]
    #[TokenRequired]
    public function getRapport(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            // récupérer limit depuis l'URL
            $limit = $request->query->getInt('limit', 10); // 10 par défaut

            $listeRapports = $this->cus->getByUtilisateur($user, 'DESC', $limit);

            $listeRapportsArray = $this->cus->transformerArray($listeRapports);

            return $this->jsonSuccess($listeRapportsArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/calendrier', name: 'api_rapports_calendrier', methods: ['GET'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function getRapportByCalendrier(Request $request): JsonResponse
    {
        try {   
            $idCalendrier = $request->query->get('idCalendrier');

            if (!$idCalendrier) {
                return $this->jsonError('Paramètre idCalendrier requis', 400);
            }
            $listeRapports = $this->cus->getByCalendrierId($idCalendrier);
            $listeRapportsArray= $this->cus->transformerArray($listeRapports);
            return $this->jsonSuccess($listeRapportsArray);
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/changerValidation', name: 'api_rapports_changer_validation', methods: ['POST'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function changerValidation(Request $request): JsonResponse
    {
        try {  
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['id'];
            $this->validatorService->validateRequiredFields($data,$requiredFields);
            $id = $data['id'];
            $rapport = $this->cus->changerStatusValidationId($id);
            $rapportArray = $rapport->toArray();
            return $this->jsonSuccess($rapportArray);
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/{idCalendrierUtilisateur}', name: 'api_rapports_modifier', methods: ['PUT'])]
    #[TokenRequired]
    public function modifierRapport(int $idCalendrierUtilisateur, Request $request): JsonResponse
    {
        try {
            $dto = $this->deserializeAndValidate(
                $request,
                ActiviteCollectionDto::class
            );
            $user = $this->getUserFromRequest($request);

            $rapportInsert = $this->cus->modifierRapport($user, $dto, $idCalendrierUtilisateur);
            $rapportArray = $this->cus->toArray($rapportInsert);
            return $this->jsonSuccess($rapportArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/historique', name: 'api_rapports_get_historique', methods: ['GET'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function getRapportHistorique(Request $request): JsonResponse
    {
        try {
            $data = [
                'idUtilisateur' => $request->query->get('idUtilisateur'),
                'idCalendrier'  => $request->query->get('idCalendrier'),
            ];

            $requiredFields = ['idUtilisateur', 'idCalendrier'];
            $this->validatorService->validateRequiredFields($data, $requiredFields);

            $idUtilisateur = $data['idUtilisateur'];
            $idCalendrier  = $data['idCalendrier'];

            $listeRapports = $this->cus->getByCalendrierAndUtilisateurDeletedAtId($idUtilisateur,$idCalendrier);
            $listeRapportsArray= $this->cus->transformerArray($listeRapports);
            return $this->jsonSuccess($listeRapportsArray);
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/recherche', name: 'api_rapports_recherche', methods: ['GET'])]
    #[TokenRequired]
    public function getRapportRecherche(Request $request): JsonResponse
    {
        try {
            $data = [
                'date' => $request->query->get('date'),
            ];

            $user = $this->getUserFromRequest($request);
            $requiredFields = ['date'];
            $this->validatorService->validateRequiredFields($data, $requiredFields);

            $date = $data['date'];
   
            $listeRapports = $this->cus->getAllCalendrierByDate($user, new \DateTimeImmutable($date),new OrderCriteria());
            $listeRapportsArray= $this->cus->transformerArray($listeRapports);
            return $this->jsonSuccess($listeRapportsArray);
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/OS', name: 'api_OS', methods: ['GET'])]
    #[TokenRequired]
    public function getAllOs(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $listeOs = $this->OSService->getByUtilisateur($user);
            $exludes = ['createdAt','deletedAt'];
            $data= $this->OSService->transformerArray($listeOs,$exludes);
            return $this->jsonSuccess($data);
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/LI', name: 'api_LI', methods: ['GET'])]
    #[TokenRequired]
    public function getAllLI(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $listeOs = $this->LIService->getByUtilisateur($user);
            $exludes = ['createdAt','deletedAt'];
            $data= $this->LIService->transformerArray($listeOs,$exludes);
            return $this->jsonSuccess($data);
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/OS', name: 'api_OS_insert', methods: ['POST'])]
    #[TokenRequired]
    public function insertOs(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto= $this->deserializeAndValidate($request, BaseNomDto::class);
            $data = $this->OSService->insertUtilisateur($user,$dto,OS::class);
            $exludes = ['createdAt','deletedAt'];
            return $this->jsonSuccess($data->toArray($exludes));
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/LI', name: 'api_LI_insert', methods: ['POST'])]
    #[TokenRequired]
    public function insertLi(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request); 
            $dto= $this->deserializeAndValidate($request, BaseNomDto::class);
            $data = $this->LIService->insertUtilisateur($user,$dto,LI::class);
            $exludes = ['createdAt','deletedAt'];
            return $this->jsonSuccess($data->toArray($exludes));
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/LI/{id}', name: 'api_LI_update', methods: ['PUT'])]
    #[TokenRequired]
    public function updateLi(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request); 
            $dto= $this->deserializeAndValidate($request, BaseNomDto::class);
            $data = $this->LIService->modifierByUtilisateur($user,$dto,LI::class,$id);
            $exludes = ['createdAt','deletedAt'];
            return $this->jsonSuccess($data->toArray($exludes));
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/LI/{id}', name: 'api_LI_delete', methods: ['DELETE'])]
    #[TokenRequired]
    public function deleteLi(Request $request, int $id): JsonResponse
    {
        try {
            
            $user = $this->getUserFromRequest($request);
            $this->LIService->deleteByUser($user, LI::class, $id);
            return $this->jsonSuccess(true);
    
            
        } catch (\Throwable $e) {
			return $this->jsonError($e->getMessage(), 400);
		} 
    }
    #[Route('/OS/{id}', name: 'api_OS_update', methods: ['PUT'])]
    #[TokenRequired]
    public function updateOs(Request $request,int $id): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto= $this->deserializeAndValidate($request, BaseNomDto::class);
            $data = $this->OSService->modifierByUtilisateur($user,$dto,OS::class,$id);
            $exludes = ['createdAt','deletedAt'];
            return $this->jsonSuccess($data->toArray($exludes));
            
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        } 
    }
    #[Route('/OS/{id}', name: 'api_OS_delete', methods: ['DELETE'])]
    #[TokenRequired]
    public function deleteOs(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $this->OSService->deleteByUser($user, OS::class, $id);
            return $this->jsonSuccess(true);
            
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        } 
    }

    #[Route('/envoyer-rappel', name: 'api_rapports_envoyer_rappel', methods: ['POST'])]
    #[TokenRequired(['Admin', 'Supervisor'])]
    public function envoyerRappel(Request $request): JsonResponse
    {
        try {
            $dto = $this->deserializeAndValidate(
                $request,
                MailRappelDTO::class
            );

            $this->mailService->sendRapportReminder(
                $dto->getDestinataire(),
                $dto->getTypeRapport(),
                $dto->getDateLimite()
            );

            return $this->jsonSuccess(['message' => 'Email de rappel envoyé avec succès']);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        } 
    }
}