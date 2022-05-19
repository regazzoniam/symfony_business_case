<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\BasketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    public function __construct(private BasketService $basketService, private ProductRepository $productRepository){ }

    #[Route('/basket', name: 'app_basket')]
    public function index(): Response
    {
        $user = $this->getUser();
        $basketEntity = $this->basketService->getBasket($user);
        dump($basketEntity);

        return $this->render('basket/index.html.twig', [
            // 'basket' => $basketEntity,
        ]);
    }

    #[Route('/basket/add/{product_id}', name: 'app_basket_add')]
    public function add($product_id): Response
    {
        $user = $this->getUser();
        $product = $this->productRepository->find($product_id);
        $this->basketService->addProductToBasket($product,$user);

        $basketEntity = $this->basketService->getBasket($user);
        dump($basketEntity);
        $products = $basketEntity->getProducts();
        dump($products);

        return $this->render('basket/index.html.twig', [
            // 'basket' => $basketEntity,
        ]);
    }
}
