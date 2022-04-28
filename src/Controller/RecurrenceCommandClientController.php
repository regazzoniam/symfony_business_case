<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RecurrenceCommandClientController extends AbstractController
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
        // // pour recupérer un user
        // dump($this->getUser());

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
        $commandNewClientsEntities = $this->commandRepository->findCommandWithNewClientsBetweenDates($minDate, $maxDate);
        $numberOfCommandsNewClientsFound = count($commandNewClientsEntities);
        
        $commandOldClientsEntities = $this->commandRepository->findCommandWithOldClientsBetweenDates($minDate, $maxDate);
        $numberOfCommandsOldClientsFound = count($commandOldClientsEntities);

        dump($commandNewClientsEntities);
        dump($numberOfCommandsNewClientsFound);//return 8 du 2020-01-01 au 201-01-01
        dump($commandOldClientsEntities);
        dump($numberOfCommandsOldClientsFound);//return 2 du 2020-01-01 au 201-01-01

        if ($numberOfCommandsOldClientsFound > 0) {
            $result = (($numberOfCommandsNewClientsFound - $numberOfCommandsOldClientsFound) / $numberOfCommandsOldClientsFound) * 100;
        }else{
            $result = 'Nous n\'avons pas de commandes associées aux anciens clients sur la période donnée';
        }
    
        return $this->json($result);
    }
}
