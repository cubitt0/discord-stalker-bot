<?php

namespace App\Repository;

use App\Entity\LastNotifiedUserStatus;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastNotifiedUserStatus>
 *
 * @method LastNotifiedUserStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LastNotifiedUserStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LastNotifiedUserStatus[]    findAll()
 * @method LastNotifiedUserStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LastNotifiedUserStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastNotifiedUserStatus::class);
    }

    public function findAllNotOfflineUsers(): array
    {
        $qb = $this->createQueryBuilder('us');
        return $qb
            ->where('us.status in (:onlineStatuses)')
            ->setParameter('onlineStatuses', [UserStatusEnum::ONLINE, UserStatusEnum::DND, UserStatusEnum::IDLE])
            ->getQuery()
            ->getResult();
    }
}
