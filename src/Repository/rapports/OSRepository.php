<?php

namespace App\Repository\rapports;

use App\Entity\rapports\OS;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class OSRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OS::class);
    }
}