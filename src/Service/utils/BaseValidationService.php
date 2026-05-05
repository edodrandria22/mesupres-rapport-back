<?php

namespace App\Service\utils;

abstract class BaseValidationService extends BaseService
{
    protected function setDateValidation(object $entity, ?\DateTimeInterface $date = null): void
    {
        if (!method_exists($entity, 'setDateValidation')) {
            throw new \LogicException(
                'L\'entité doit avoir une méthode setDateValidation()'
            );
        }

        $entity->setDateValidation($date ?? new \DateTimeImmutable());
    }

    public function valider(object $entity, bool $flush = true): object
    {
        $this->setDateValidation($entity);

        return $this->save($entity, $flush);
    }

    public function validerById(int $id): object
    {
        $entity = $this->getVerifierById($id);

        return $this->valider($entity);
    }
    public function devaliderById(int $id): object
    {
        $entity = $this->getVerifierById($id);

        return $this->devalider($entity);
    }
    
    protected function devalider(object $entity): object
    {
        $this->setDateValidation($entity, null);

        return $this->save($entity);
    }
}