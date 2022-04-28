<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use App\Repository\VisitRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PercentConversionBasketController extends AbstractController
{
    //on crée des repo pour pouvoir faire des requetes sur la database
    private CommandRepository $commandRepository;
    private VisitRepository $visitRepository;

    //on met le repo dans le construct !!
    public function __construct(CommandRepository $commandRepository, VisitRepository $visitRepository) {
        $this->commandRepository = $commandRepository;
        $this->visitRepository = $visitRepository;
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

        //on applique la fonction findBasketsBetweenDates() à nos objets date
        $basketEntities = $this->commandRepository->findBasketsBetweenDates($minDate, $maxDate);
        //on applique la fonction findVisitsBetweenDates() à nos objets date
        $visitEntities = $this->visitRepository->findVisitsBetweenDates($minDate, $maxDate);

        // //on compte le nombre d'entités trouvées
        $numberOfBasketFound = count($basketEntities);
        $numberOfVisitFound = count($visitEntities);

        $result = ($numberOfBasketFound * 100)/ $numberOfVisitFound;

        // //on dump $visitEntities que l'on retrouve dans profiller =>last10
        dump($result);
        return $this->json($result);
    }
}
