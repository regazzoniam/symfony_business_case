<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findUserBetweenDates($minDate, $maxDate){

        //permet de créer un select * from Dates
        return $this->createQueryBuilder('u')
        //ajout d'une fct where qui permet de recup command à partir de sa date (: sert à déclarer des variables)
        ->where('u.createdAt > :date_min')
        ->andWhere('u.createdAt < :date_max')
        //remplacer la variable {{date_min}} & {{date_max}} par mes objets dates
        ->setParameter('date_min', $minDate)
        ->setParameter('date_max', $maxDate)
        //permet d'executer la query afin de recup nos entités
        ->getQuery()->getResult();

    }

    // fonction de pagination
    public function getQbAll(){
        return $this->createQueryBuilder('u')
        ->orderBy('u.id')
        ->getQuery()->getResult();
    }

    // public function findEmailViaInput($datas){
    //     if($datas['email'] !== null){
    //         return $this->createQueryBuilder('u')
    //         //ajout d'une fct where qui permet de vérifier si un mail existe
    //         ->where('u.email = :email_informed')
    //         // on remplace le paramètre par le mail renseigné dans le formulaire
    //         ->setParameter(':email_informed', $datas['email'])
    //         //permet d'executer la query afin de recup nos entités
    //         ->getQuery()->getResult();
    //     }
    // }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
