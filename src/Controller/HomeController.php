<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
//abstract controller permet de faire appel à la fonction render
{
    // constucteur permettant de lister les repository (lien entre entité et BDD) à utiliser
    public function __construct(private ProductRepository $productRepository) { }
    // route définie pour accéder à la vue (url et nom route: utile au path)
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // tous les produits
        $products = $this->productRepository->bestProducts();

        // on renvoie sur la vue contenue dans template/home/index.html.twig (début du chemin dans twig.yaml)
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
