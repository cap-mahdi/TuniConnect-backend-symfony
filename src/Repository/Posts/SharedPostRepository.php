<?php

namespace App\Repository\Posts;

use App\Entity\Accounts\Member;
use App\Entity\Posts\SharedPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SharedPost>
 *
 * @method SharedPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method SharedPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method SharedPost[]    findAll()
 * @method SharedPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SharedPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedPost::class);
    }

    public function save(SharedPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SharedPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return SharedPost[] Returns an array of SharedPost objects
     */
    public function findTimelinePost($id,$limit = 20,$offset = 0): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT sp FROM App\Entity\Posts\SharedPost sp
     WHERE sp.sharer = :id OR sp.sharer IN (
         SELECT f.id FROM App\Entity\Accounts\Member m
         JOIN m.friends f
         WHERE m.id = :id
     )
     ORDER BY sp.date DESC'
        )->setParameter('id', $id);
        $results = $query->setMaxResults($limit)->setFirstResult($offset)->getResult();
        return $results;

    }



//    public function findOneBySomeField($value): ?SharedPost
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
