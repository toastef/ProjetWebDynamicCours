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

    /**
     * Va permettre de retrouver les peintures liké par un user
     * @param $userId
     * @return float|int|mixed|string
     */
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

    /**
     * Permet de retrouver les tableaux d'un vendeur
     * @param $userId
     * @return float|int|mixed|string
     */
    public function findTablesBySellerRole($userId)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT t 
             FROM App\Entity\Painting t
              WHERE t.vendeur = :seller '
        )->setParameter('seller', $userId);

        return $query->getResult();
    }

}
