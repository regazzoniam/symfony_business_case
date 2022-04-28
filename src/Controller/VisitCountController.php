<?php

namespace App\Controller;

use App\Repository\VisitRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class VisitCountController extends AbstractController
{
    //on crée des repo pour pouvoir faire des requetes sur la database
    private VisitRepository $visitRepository;

    //on met le repo dans le construct !!
    public function __construct(VisitRepository $visitRepository) {
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

        //on applique la fonction findVisitsBetweenDates() à nos objets date
        $visitEntities = $this->visitRepository->findVisitsBetweenDates($minDate, $maxDate);

        //on compte le nombre d'entités trouvées
        $numberOfEntitiesFound = count($visitEntities);

        //on dump $visitEntities que l'on retrouve dans profiller =>last10
        dump($visitEntities);
        return $this->json($numberOfEntitiesFound);
    }
}
