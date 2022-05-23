<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\BasketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    public function __construct(private BasketService $basketService, private ProductRepository $productRepository, private EntityManagerInterface $em){ }

    #[Route('/basket', name: 'app_basket')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        $products = $basketEntity->getProducts();
        // obtenir le nombre de produits dans le panier
        $numberOfProduct = count($products);
        // on update le totalPrice de la commande quand on paiera !!
        }else{
            $basketEntity = NULL;
            $products = NULL;
            $numberOfProduct = NULL;
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
    #[Route('/basket/payment', name: 'app_basket_payment')]
    public function payment(): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        // on passe le statut de la commande à payée
        $basketEntity->setStatus(200);
        // on attribut à la commande son prix total
        $products = $basketEntity->getProducts();
        $numberOfProduct = count($products);
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        $basketEntity->setTotalPrice($totalPrice);
        dump($basketEntity);
        $this->em->persist($basketEntity);
        $this->em->flush();
        }else{
            $basketEntity = NULL;
            $products = NULL;
            $numberOfProduct = NULL;
        }
        return $this->render('basket/payment.html.twig', [
        ]);
    }
    #[Route('/basket/payment/send', name: 'app_basket_payment_send')]
    public function paymentSend(): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        // on passe le statut de la commande à payée
        $basketEntity->setStatus(300);
        // on attribut à la commande son prix total
        $products = $basketEntity->getProducts();
        $numberOfProduct = count($products);
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        $basketEntity->setTotalPrice($totalPrice);
        dump($basketEntity);
        $this->em->persist($basketEntity);
        $this->em->flush();
        }else{
            $basketEntity = NULL;
            $products = NULL;
            $numberOfProduct = NULL;
        }
        return $this->render('basket/paymentSend.html.twig', [
        ]);
    }
    #[Route('/basket/payment/refund', name: 'app_basket_payment_refund')]
    public function paymentRefund(): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        // on passe le statut de la commande à payée
        $basketEntity->setStatus(400);
        // on attribut à la commande son prix total
        $products = $basketEntity->getProducts();
        $numberOfProduct = count($products);
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        $basketEntity->setTotalPrice($totalPrice);
        dump($basketEntity);
        $this->em->persist($basketEntity);
        $this->em->flush();
        }else{
            $basketEntity = NULL;
            $products = NULL;
            $numberOfProduct = NULL;
        }
        return $this->render('basket/paymentRefund.html.twig', [
        ]);
    }
    #[Route('/basket/payment/cancel', name: 'app_basket_payment_cancel')]
    public function paymentCancel(): Response
    {
        $user = $this->getUser();
        if ($user != null){
        $basketEntity = $this->basketService->getBasket($user);
        // on passe le statut de la commande à payée
        $basketEntity->setStatus(500);
        // on attribut à la commande son prix total
        $products = $basketEntity->getProducts();
        $numberOfProduct = count($products);
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        $basketEntity->setTotalPrice($totalPrice);
        dump($basketEntity);
        $this->em->persist($basketEntity);
        $this->em->flush();
        }else{
            $basketEntity = NULL;
            $products = NULL;
            $numberOfProduct = NULL;
        }
        return $this->render('basket/paymentCancel.html.twig', [
        ]);
    }
}
