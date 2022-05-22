<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Command>
 *
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Command $entity, bool $flush = true): void
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
    public function remove(Command $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findCommandsBetweenDates($minDate, $maxDate){

        //permet de créer un select * from Dates
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->where('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')
        ->andWhere('c.status = 200 OR c.status = 300 OR c.status = 400 OR c.status = 500')
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();

    }

    public function findTotalPriceCommandsBetweenDates($minDate, $maxDate){

        //permet de créer un select * from Dates
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->where('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')
        ->andWhere('c.status = 200 OR c.status = 300')
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();

    }

    
    public function findBasketsBetweenDates($minDate, $maxDate){

        //permet de créer un select * from Dates
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->where('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')
        ->andWhere('c.status = 100')
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();
    }


    public function findAverageBasketAmountBetweenDates($minDate, $maxDate){

        //permet de créer un select * from Dates
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->where('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')
        ->andWhere('c.status = 100')
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();
    }

    public function findCommandWithNewClientsBetweenDates($minDate, $maxDate){

        //permet de créer un select * from command
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->innerJoin('c.user','u')
        ->where('u.createdAt > :date_min')
        ->andWhere('u.createdAt < :date_max')
        ->andWhere('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')

        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();
    }

    public function findCommandWithOldClientsBetweenDates($minDate,$maxDate){

        //permet de créer un select * from command
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->innerJoin('c.user','u')
        ->where('u.createdAt < :date_min')
        ->andWhere('c.createdAt > :date_min')
        ->andWhere('c.createdAt < :date_max')
        
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();
    }


    public function getBasketByUser($user){

        //permet de créer un select * from command
        return $this->createQueryBuilder('c')
        //ajout d'une fct where qui permet de recup command à partir de l'user et du status
        ->innerJoin('c.user','u')
        ->where('u = :user')
        ->andWhere('c.status = :status')
        //remplacer les variables
        ->setParameter('user', $user)
        ->setParameter('status', 100)
        ->setMaxResults(1)
        //permet d'executer la query afin de recup notre result (sous forme d'une entité ou null)
        ->getQuery()->getOneOrNullResult();
    }

    // fonction de pagination
    public function getQbAll(){
        return $this->createQueryBuilder('c')
        ->orderBy('c.createdAt','DESC')
        ->getQuery()->getResult();
    }

    // /**
    //  * @return Command[] Returns an array of Command objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Command
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
