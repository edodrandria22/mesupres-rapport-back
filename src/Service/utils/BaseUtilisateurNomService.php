<?php

namespace App\Service\utils;

use App\Dto\utils\BaseNomDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


abstract class BaseUtilisateurNomService extends BaseService
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    /**
     * Chaque classe fille doit retourner son repository
     */
    abstract protected function getRepository();

    public function getByUtilisateur(Utilisateurs $utilisateur, ?OrderCriteria $order = new OrderCriteria()): array
    {
        $conditions = [
                new ConditionCriteria('utilisateur', $utilisateur->getId(), '='),
            ];
        
        return $this->search($conditions, $order);
    }
    public function insertUtilisateur(
        Utilisateurs $utilisateur,
        BaseNomDto $baseNomDto,
        string $entityClass
    ): object {
        // Création dynamique
        $entity = new $entityClass();

        // Hydratation de base
        if (method_exists($entity, 'setName')) {
            $entity->setName($baseNomDto->name);
        }

        if (method_exists($entity, 'setUtilisateur')) {
            $entity->setUtilisateur($utilisateur);
        }

        $this->save($entity);

        return $entity;
    }
    public function modifierByUtilisateur(
        Utilisateurs $utilisateur,
        BaseNomDto $baseNomDto,
        string $entityClass,
        int $id
    ): object {
        // Création dynamique
        $entity = $this->getVerifierById($id);
        if (!method_exists($entity, 'getUtilisateur')) {
            throw new Exception("L'entité $entityClass n'a pas de relation utilisateur définie");
        }

        $utilisateurPrecedent = $entity->getUtilisateur();
        if (!$utilisateurPrecedent || $utilisateur->getId() !== $utilisateurPrecedent->getId()) {
            throw new Exception("Seul l'utilisateur concerné peut modifier le $entityClass");
        }
        
        // Hydratation de base
        if (method_exists($entity, 'setName')) {
            $entity->setName($baseNomDto->name);
        }


        $this->save($entity);

        return $entity;
    }
    public function deleteByUser(
        Utilisateurs $utilisateur,
        string $entityClass,
        int $id
    ): void {
        $entity = $this->getVerifierById($id);
        if (!method_exists($entity, 'getUtilisateur')) {
            throw new Exception("L'entité $entityClass n'a pas de relation utilisateur définie");
        }

        $utilisateurPrecedent = $entity->getUtilisateur();
        if (!$utilisateurPrecedent || $utilisateur->getId() !== $utilisateurPrecedent->getId()) {
            throw new Exception("Seul l'utilisateur concerné peut supprimer le $entityClass");
        }

        $this->delete($entity);
    }
    
}