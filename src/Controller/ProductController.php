<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private PaginatorInterface $paginator){}

    #[Route('/all', name: 'app_product_all')]
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
// produits pour chat
    #[Route('/cat', name: 'app_product_cats')]
    public function cats(Request $request): Response
    {
        $qb = $this->productRepository->catProducts();

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('product/cats.html.twig', [
            'pagination' => $pagination
        ]);
    }

    // produits pour chien
    #[Route('/dog', name: 'app_product_dogs')]
    public function dogs(Request $request): Response
    {
        $qb = $this->productRepository->dogProducts();

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('product/dogs.html.twig', [
            'pagination' => $pagination
        ]);
    }
    // #[Route('/cats', name: 'app_product_cats')]
    // public function cats(): Response
    // {
    //     $catsProducts = $this->productRepository->catProducts();
    //     dump($catsProducts);
    //     return $this->render('product/cats.html.twig', [
    //         'catsProducts' => $catsProducts
    //     ]);
    // }
}
