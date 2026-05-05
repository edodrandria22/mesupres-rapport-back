<?php

namespace App\Repository\rapports;

use App\Entity\rapports\LI;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class LIRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LI::class);
    }
}