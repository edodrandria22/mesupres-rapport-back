<?php

namespace App\Service\utilisateurs;

use App\Dto\utilisateurs\UtilisateurDto;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\utilisateurs\RolesRepository;
use App\Service\utils\BaseService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\utilisateurs\UtilisateursRepository;
use Exception;
use App\Entity\rapports\Calendriers;
use App\Dto\utils\OrderCriteria;
use App\Service\rapports\CalendriersService;
class UtilisateursService extends BaseService
{
    
    private UtilisateursRepository $utilisateurRepository;
    private RolesRepository $roleRepository;
    private CalendriersService $calendrierService;


    public function __construct(EntityManagerInterface $em, UtilisateursRepository $utilisateurRepository, RolesRepository $roleRepository, CalendriersService $calendrierService)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->roleRepository = $roleRepository;
        $this->calendrierService = $calendrierService;
        parent::__construct($em);
    }
    public function getRepository()
    {
        return $this->utilisateurRepository;
    }

    /**
     * @param Utilisateurs $user L'utilisateur à créer
     * @param string $plainPassword Le mot de passe en clair
     */
    public function createUserByRole(Utilisateurs $user): Utilisateurs
    {

        $plainPassword = $user->getMdp();
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $user->setMdp($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
    public function getAllUsers(OrderCriteria $criteria): array
    {
        return $this->utilisateurRepository->getAllParOrdre($criteria);
    }
    public function getUserById(int $id): ?Utilisateurs
    {
        return $this->utilisateurRepository->find($id);
    }
    public function updateUser($idUser, array $data): Utilisateurs
    {
        $user = $this->utilisateurRepository->find($idUser);
        if (!$user) {
            throw new Exception('Utilisateur non trouvé pour id=' . $idUser);
        }

        if (isset($data['email'])) {
            $newEmail = $data['email'];
            if ($newEmail !== $user->getEmail()) {
                $existingUser = $this->utilisateurRepository->findOneBy(['email' => $newEmail]);
                if ($existingUser) {
                    throw new Exception('CONFLIT_EMAIL : Cet email est déjà attribué à un autre compte.');
                }
                $user->setEmail($newEmail);
            }
        }

        if (isset($data['entite'])) {
            $user->setEntite($data['entite']);
        }

        if (isset($data['idRole'])) {
            $role = $this->roleRepository->find($data['idRole']);
            if (!$role) {
                throw new \InvalidArgumentException('Rôle introuvable pour l\'ID fourni');
            }
            $user->setRole($role);
        }

        if (isset($data['mdp']) && !empty($data['mdp'])) {
            $hashedPassword = password_hash($data['mdp'], PASSWORD_BCRYPT);
            $user->setMdp($hashedPassword);
        }
        
        if (isset($data['rang'])) {
            $user->setRang($data['rang']);
        }
        if (isset($data['sigle'])) {
            $user->setSigle($data['sigle']);
        }
        if (isset($data['emailCopie'])) {
            $user->setEmailCopie($data['emailCopie']);
        }
        
        $this->em->flush();

        return $user;
    }
    public function createUser(Utilisateurs $user, $role_id = 2): Utilisateurs
    {
        $role = $this->roleRepository->find($role_id); // 2 correspond au rôle "Utilisateur"
        if (!$role) {
            throw new Exception("Role non trouvé pour id=" . $role_id);
        }
        $user->setRole($role);
        return $this->createUserByRole($user);
    }

    public function login(string $email, string $plainPassword): ?Utilisateurs
    {
        $user = $this->utilisateurRepository->login($email, $plainPassword);

        return $user;
    }
    public function transformerArray(array $utilisateurs, array $exclude = []): array
    {
        $result = [];
        foreach ($utilisateurs as $index => $utilisateur) {
            $result[$index] = $utilisateur->toArray($exclude);
        }
        return $result;
    }

    public function insertDto(UtilisateurDto $utilisateurDto): Utilisateurs
    {
        $result = new Utilisateurs();
        $result->setEmail($utilisateurDto->getEmail());
        $result->setMdp($utilisateurDto->getMdp());
        $result->setEntite($utilisateurDto->getEntite());
        $result->setRang($utilisateurDto->getRang());
        $role = $this->roleRepository->find($utilisateurDto->getIdRole());
        if (!$role) {
            throw new Exception("Role non trouvé pour id=" . $utilisateurDto->getIdRole());
        }
        $result->setRole($role);
        $result->setSigle($utilisateurDto->getSigle());
        $result->setEmailCopie($utilisateurDto->getEmailCopie());
        $result = $this->createUserByRole($result); 
        return $result;
    }
    public function getUserByIdArray(int $id): array
    {
        $result = $this->getUserById($id);
        return $result->toArray();
    }
    public function getUsersNotInCalendrier(Calendriers $calendrier): array
    {
        return $this->utilisateurRepository->findUsersNotInCalendrier($calendrier);
    }
    public function getUsersNotInCalendrierId($calendrierId): array
    {
        $calendrier = $this->calendrierService->getById($calendrierId);
        if (!$calendrier) {
            throw new Exception("Calendrier non trouvé pour id=" . $calendrierId);
        }
        return $this->utilisateurRepository->findUsersNotInCalendrier($calendrier);
    }
    public function changerMdp(Utilisateurs $user,$nouveauMdp): Utilisateurs
    {
        $hashedPassword = password_hash($nouveauMdp, PASSWORD_BCRYPT);

        $user->setMdp($hashedPassword);
        $user->setDateValidation(new DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

}
