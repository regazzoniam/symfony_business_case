<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository){}
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        // les plus vendus
        $mostSoldproducts = $this->productRepository->mostSoldProducts();
        dump($mostSoldproducts);

        // les mieux notés
        $bestProducts = $this->productRepository->bestProducts();
        dump($bestProducts);
        return $this->render('product/index.html.twig', [
            'mostSoldProducts' => $mostSoldproducts,
            'bestProducts' => $bestProducts,
        ]);
    }
}
