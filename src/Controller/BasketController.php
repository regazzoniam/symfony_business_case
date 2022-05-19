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
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        $products = $basketEntity->getProducts();
        // obtenir le nombre de produits dans le panier
        $numberOfProduct = count($products);
        // mettre à jour le prix total du panier : mettre dans un filtre twig
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        dump($totalPrice);
        // on update le totalPrice de la commande quand on paiera !!
        // /mettre à jour le prix total du panier
        }

        return $this->render('basket/index.html.twig', [
            'basketEntity' => $basketEntity,
            'products' => $products,
            'numberOfProduct' => $numberOfProduct
        ]);
    }

    #[Route('/basket/add/{product_id}', name: 'app_basket_add')]
    public function add($product_id): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $product = $this->productRepository->find($product_id);
        $this->basketService->addProductToBasket($product,$user);
        }
        return $this->redirectToRoute('app_basket');
    }

    #[Route('/basket/remove/{product_id}', name: 'app_basket_remove')]
    public function remove($product_id): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $product = $this->productRepository->find($product_id);
        $this->basketService->removeProductFromBasket($product,$user);
        }
        return $this->redirectToRoute('app_basket');
    }
}
