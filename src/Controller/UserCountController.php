<?php

namespace App\Controller;

use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserCountController extends AbstractController
{
    //on crée des repo pour pouvoir faire des requetes sur la database
    private UserRepository $userRepository;

    //on met le repo dans le construct !!
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
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

        //on applique la fonction findUserBetweenDates() à nos objets date
        $userEntities = $this->userRepository->findUserBetweenDates($minDate, $maxDate);

        //on compte le nombre d'entités trouvées
        $numberOfEntitiesFound = count($userEntities);

        //on dump $visitEntities que l'on retrouve dans profiller =>last10
        dump($userEntities);
        return $this->json(['data' => $numberOfEntitiesFound]);
    }
}
