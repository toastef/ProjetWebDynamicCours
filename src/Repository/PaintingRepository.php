<?php

namespace App\Repository;

use App\Entity\Painting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Painting>
 *
 * @method Painting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Painting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Painting[]    findAll()
 * @method Painting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaintingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Painting::class);
    }

    public function save(Painting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Painting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLikedByUser($userId)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT p
             FROM App\Entity\Painting p
             JOIN p.likes l
             WHERE l.user = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }
//    /**
//     * @return Painting[] Returns an array of Painting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


}
