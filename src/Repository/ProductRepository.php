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
    // pour avoir les 4 produits les mieux notés
        public function bestProducts(int $limit = 4){
            return $this->createQueryBuilder('p')
                // jointure pour pouvoir intéragir avec les ptés de l'entité renviews
                ->join('p.reviews','r')
                // trie par ordre décroisssant le nom des produits récupérés
                ->orderBy('r.note', 'DESC')
                // limiter les résultats à 4
                ->setMaxResults($limit)
                // transformer la requête DQL en SQL
                ->getQuery()
                // éxécuter la requête
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
            // jointure pour pouvoir intéragir avec les ptés de l'entité categories
            ->join('p.categories','c')
            // condition sur le label de la catégorie - requête préparée avec marqueur nommé
            ->where('c.label LIKE :value')
            // remplace marqueur nommé par sa valeur
            ->setParameter('value', '%Chat%')
            // trie par ordre croissant
            ->orderBy('p.label', 'ASC')
            // transformer la requête DQL en SQL
            ->getQuery()
            // éxécuter la requête
            ->getResult()
            ;
    }

        /**
    * @return Product[] Returns an array of Product objects
    */
    // pour avoir les produits pour chiens
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
