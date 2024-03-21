<?php

namespace App\Repository;

use App\Entity\StalkSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method StalkSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method StalkSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method StalkSettings[]    findAll()
 * @method StalkSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StalkSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StalkSettings::class);
    }
}
