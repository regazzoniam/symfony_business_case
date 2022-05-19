<?php


namespace App\Service;

use App\Entity\Command;
use App\Entity\User;
use App\Repository\CommandRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BasketService
{
    public function __construct(private CommandRepository $commandRepository, private EntityManagerInterface $em){}

    public function getBasket(User $user): Command {
        
        if($user != null){// on vérifie la connexion de l'user
            $user->getCommands();
            $basketEntity = $this->commandRepository->getBasketByUser($user);
        }
        if($basketEntity === null){// si il y a aucun panier en cours relié à ce user
            $basketEntity = new Command();//on crée une commande 
            $basketEntity->setStatus(100);//avec le status = 100 (panier)
            $basketEntity->setUser($user);//on lui associe le user passé en paramètre
            $basketEntity->setTotalPrice(0);//on lui définit un totalPrice à 0 car le panier vient d'être créé
            $basketEntity->setNumCommand((int)uniqid());//on définit un numéro de commande (série de chiffres aléatoire)
            $basketEntity->setCreatedAt(new DateTime());//on définit une date de commande
            $this->em->persist($basketEntity);
            $this->em->flush();
        }
        return $basketEntity;
    }

    public function addProductToBasket($productEntity, User $user){
        if ($user != null){
            $basketEntity = $this->getBasket($user);//on applique la fonction getBasket contenue dans le service BasketService (obtenir les commandes associées à un user qui ont le status = 100) 
            $basketEntity->addProduct($productEntity);// on ajoute un produit à l'entité panier (qui est une commande avec un status 100)
            $this->em->persist($productEntity);
            $this->em->flush();
        }
    }

    public function removeProductFromBasket($productEntity, User $user){
        if ($user != null){
            $basketEntity = $this->getBasket($user);//on applique la fonction getBasket contenue dans le service BasketService (obtenir les commandes associées à un user qui ont le status = 100) 
            $basketEntity->removeProduct($productEntity);// on supprime un produit à l'entité panier (qui est une commande avec un status 100)
            $this->em->persist($basketEntity);
            $this->em->flush();
        }
    }

}