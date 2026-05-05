<?php

namespace App\Controller\Api\utilisateurs;

use App\Controller\Api\utils\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Dto\utilisateurs\UtilisateurDto;
use App\Dto\utils\OrderCriteria;

#[Route('/utilisateurs')]
class UtilisateursController extends BaseApiController
{
    #[Route('', name: 'user', methods: ['GET'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function getUtilisateur(Request $request): JsonResponse
    {
        try {

            $utilisateurs = $this->utilisateursService->getAllUsers(new OrderCriteria());
            $exclude = ['createdAt', 'deletedAt', 'mdp'];
            $usersArray = $this->utilisateursService->transformerArray($utilisateurs, $exclude);

            return $this->jsonSuccess($usersArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('', name: 'api_utilisateur_create', methods: ['POST'])]
    #[TokenRequired(['Admin'])]
    public function createUser(Request $request): JsonResponse
    {

        try {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                UtilisateurDto::class,
                'json'
            );

            // Valider le DTO
            $errors = $this->validator->validate($dto);

            if (count($errors) > 0) {
                // $errorMessages = [];
                $messages = [];

                foreach ($errors as $error) {
                    $property = $error->getPropertyPath();
                    $message = $error->getMessage();

                    // erreurs par champ
                    // $errorMessages[$property][] = $message;

                    // message global
                    $messages[] = sprintf('%s : %s', $property, $message);
                }
                $erreurMessage = 'Erreur de validation : ' . implode(' | ', $messages);

                return $this->jsonError($erreurMessage, Response::HTTP_BAD_REQUEST);
            }

            $userArray = $this->utilisateursService->insertDto($dto);
            return $this->jsonSuccess($userArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }

    #[Route('/{id}', name: 'api_utilisateur_get_one', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Admin'])]
    public function getOneUser(int $id): JsonResponse
    {
        $user = $this->utilisateursService->getUserById($id);

        if (!$user) {
            return $this->jsonError("Utilisateur non trouvé", 404);
        }
        $userArray = $user->toArray([], true);
        return $this->jsonSuccess($userArray);
    }

    #[Route('/{id}', name: 'api_utilisateur_update', methods: ['PUT'])]
    #[TokenRequired(['Admin'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->jsonError('Données invalides ou JSON mal formé', 400);
        }

        try {
            $user = $this->utilisateursService->updateUser($id, $data);
            return $this->jsonSuccess($user->toArray());
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'CONFLIT_EMAIL')) {
                return $this->jsonError('Cet email est déjà utilisé par un autre compte.', 409);
            }
            return $this->jsonError($e->getMessage(), 400);
        }
    }



    #[Route('/login', name: 'api_utilisateur_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['email', 'password'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Champs requis manquants : ' . implode(', ', $missingFields),
                'missingFields' => $missingFields
            ], 400);
        }

        $email = $data['email'];
        $plainPassword = $data['password'];


        // 🔑 Vérification du login via le repository
        $user = $this->utilisateursService->login($email, $plainPassword);

        if (!$user) {
            return $this->jsonError('Identifiants invalides', 404);
        }



        // $claims = [
        //     'id' => $user->getId(),
        //     'email' => $user->getEmail(),
        //     'role' => $user->getRole()->getName(),
        //     'entite' => $user->getEntite(),
        //     'dateValidation'=> $user->getDateValidation()->format('Y-m-d H:i:s'),
            
        // ];
        $claims= $user->toArray(['mdp','createdAt','deletedAt']);

        $tokenDuration = $this->params->get('jwt_token_duration');

        $token = $this->jwtManager->createToken($claims, $tokenDuration);
        $tokenString = $token->toString();
        $data = [
            'user' => $claims,
            'token' => $tokenString
        ];
        return $this->jsonSuccess($data);
        } catch (\Exception $e) {
            // error_log($e->getMessage());
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/calendrierRetard', name: 'user_calendrier_retard', methods: ['GET'])]
    #[TokenRequired(['Admin','Supervisor'])]
    public function getUtilisateurCalendrierRetard(Request $request): JsonResponse
    {
        try {
            $idCalendrier = $request->query->get('idCalendrier');

            if (!$idCalendrier) {
                return $this->jsonError('Paramètre idCalendrier requis', 400);
            }
            $utilisateurs = $this->utilisateursService->getUsersNotInCalendrierId($idCalendrier);
            $exclude = ['createdAt', 'deletedAt', 'mdp'];
            $usersArray = $this->utilisateursService->transformerArray($utilisateurs, $exclude);

            return $this->jsonSuccess($usersArray);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    
    #[Route('/changerMdp', name: 'user_changer_mdp', methods: ['POST'])]
    #[TokenRequired]
    public function changerMdp(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUserFromRequest($request);
            $requiredFields = ['mdp'];
            $this->validatorService->validateRequiredFields($data, $requiredFields);

            $nouveauMdp = $data['mdp'];
            $user = $this->utilisateursService->changerMdp($user, $nouveauMdp);
            
            return $this->jsonSuccess([
                'message' => 'Mot de passe modifié avec succès',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'role' => $user->getRole()->getName(),
                    'entite' => $user->getEntite()
                ]
            ]);
            
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
}
