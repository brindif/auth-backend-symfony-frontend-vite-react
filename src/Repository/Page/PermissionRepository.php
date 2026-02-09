<?php

namespace App\Repository\Page;

use App\Entity\Auth\User;
use App\Entity\Page\Tab as TabEntity;
use App\ApiResource\Page\Tab as  TabResouce;
use App\Entity\Page\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\PermissionEnum;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findOneForUserAndTabId(int $tabId, User $user): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tab', 't')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.id = :tabId')
            ->setParameter('tabId', $tabId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneForUserAndNoteId(int $noteId, User $user): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tab', 't')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('t.notes', 'n')
            ->andWhere('n.id = :noteId')
            ->setParameter('noteId', $noteId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findByTab(TabEntity $tab, User $user): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.tab = :tab')
            ->setParameter('tab', $tab)
            ->andWhere('p.user != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Permission[] Returns an array of Permission objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Permission
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
