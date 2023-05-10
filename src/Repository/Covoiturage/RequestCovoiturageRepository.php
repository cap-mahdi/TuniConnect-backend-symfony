<?php

namespace App\Repository\Covoiturage;

use App\Entity\Covoiturage\RequestCovoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestCovoiturage>
 *
 * @method RequestCovoiturage|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestCovoiturage|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestCovoiturage[]    findAll()
 * @method RequestCovoiturage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestCovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestCovoiturage::class);
    }

    public function save(RequestCovoiturage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RequestCovoiturage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RequestCovoiturage[] Returns an array of RequestCovoiturage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RequestCovoiturage
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function removeById(int $id, bool $flush = false): void
    {
        $qb = $this->createQueryBuilder('e');
        $qb->delete()
            ->where('e.covoiturage = :id')
            ->setParameter('covoiturage', $id)
            ->getQuery()
            ->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
