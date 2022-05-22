<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
    * @return Product[] Returns an array of Product objects
    */
    // pour avoir les produits les mieux notés
        public function bestProducts(int $limit = 4){
            return $this->createQueryBuilder('p')
                ->join('p.reviews','r')
                ->orderBy('r.note', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
                ;
        }

    /**
    * @return Product[] Returns an array of Product objects
    */
    // pour avoir les meilleures ventes
        public function mostSoldProducts(int $limit = 4){
            return $this->createQueryBuilder('p')
                ->select('p','COUNT(c) AS numberCommand')
                ->join('p.commands','c')
                ->groupBy('p.id')
                ->orderBy('numberCommand', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
                ;
        }


    // fonction de pagination
        public function getQbAll(){
            return $this->createQueryBuilder('p')
            ->orderBy('p.id')
            ->getQuery()->getResult();
    }
    
    /**
    * @return Product[] Returns an array of Product objects
    */
    // pour avoir les produits pour chats
    public function catProducts(){
        return $this->createQueryBuilder('p')
            ->select('p','c')
            ->join('p.categories','c')
            ->where('c.label LIKE :value')
            ->setParameter('value', '%Chat%')
            ->orderBy('p.label', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

        /**
    * @return Product[] Returns an array of Product objects
    */
    // pour avoir les produits pour chats
    public function dogProducts(){
        return $this->createQueryBuilder('p')
            ->select('p','c')
            ->join('p.categories','c')
            ->where('c.label LIKE :value')
            ->setParameter('value', '%Chien%')
            ->orderBy('p.label', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    


    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
