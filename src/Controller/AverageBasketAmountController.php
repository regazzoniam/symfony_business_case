<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AverageBasketAmountController extends AbstractController
{
    //on crée des repo pour pouvoir faire des requetes sur la database
    private CommandRepository $commandRepository;

    //on met le repo dans le construct !!
    public function __construct(CommandRepository $commandRepository) {
        $this->commandRepository = $commandRepository;
    }

    //Request : HttpFoundation\Request
    public function __invoke(Request $request)
    {
        // pour recupérer un user
        dump($this->getUser());

        //création de 2 dates en string
        $minDateString = $request->query->get('min_date');
        $maxDateString = $request->query->get('max_date');

        //création de 2 objets DateTime pour pouvoir faire notre requête via Symfony
        $minDate = new DateTime($minDateString);
        $maxDate = new DateTime($maxDateString);

        //on dump les 2 objets
        dump($minDate);
        dump($maxDate);

        //on applique la fonction findCommandsBetweenDates() à nos objets date
        $commandEntities = $this->commandRepository->findAverageBasketAmountBetweenDates($minDate, $maxDate);

        $numberOfEntitiesFound = count($commandEntities);

        $incrementTotalPrice = 0;

        foreach($commandEntities as $commandEntity){
            
            $incrementTotalPrice += $commandEntity->getTotalPrice();
        }
        if ($numberOfEntitiesFound != 0) {
            $result = $incrementTotalPrice / $numberOfEntitiesFound;
        }else{
            $result = "Erreur : Division par zéro";
        }

        dump($result);

        return $this->json(['data' => $result]);
    }
}
