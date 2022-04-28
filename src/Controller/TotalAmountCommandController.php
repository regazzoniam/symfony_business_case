<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TotalAmountCommandController extends AbstractController
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
        $commandEntities = $this->commandRepository->findTotalPriceCommandsBetweenDates($minDate, $maxDate);

        $incrementTotalPrice = 0;

        foreach($commandEntities as $commandEntity){
            
            $incrementTotalPrice += $commandEntity->getTotalPrice();
        }

        dump($incrementTotalPrice);
        //on dump $visitEntities que l'on retrouve dans profiller =>last10
        // dump($commandEntities);
        return $this->json($incrementTotalPrice);
    }
}
