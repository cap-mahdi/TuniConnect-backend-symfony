<?php

namespace App\Repository\Notifications;

use App\Entity\Notifications\CovoiturageNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CovoiturageNotification>
 *
 * @method CovoiturageNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method CovoiturageNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method CovoiturageNotification[]    findAll()
 * @method CovoiturageNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CovoiturageNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovoiturageNotification::class);
    }

    public function save(CovoiturageNotification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CovoiturageNotification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CovoiturageNotification[] Returns an array of CovoiturageNotification objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CovoiturageNotification
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
