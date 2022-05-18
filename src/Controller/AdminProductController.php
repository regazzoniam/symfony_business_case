<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private ProductRepository $productRepository, private PaginatorInterface $paginator)
    { }

    #[Route('/admin/product/all', name: 'app_admin_product_all')]
    public function index(Request $request): Response
    {
        $qb = $this->productRepository->getQbAll();
        $pagination = $this->paginator->paginate(
            // 1er argument : query builder
            $qb,
            // 2eme argument : sur quel page se trouve (1: page par défaut)
            $request->query->getInt('page',1),
            // 3eme argument : nombre de résultats par page
            5
        );

        return $this->render('admin_product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function add(Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, new Product());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            // pour uploader l'image
            $image = $form->get('image')->getData();
            if ($image){
                // pour prendre le nom de l'image chargée sans son extension
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // pour obtenir un slug : on récup le $originalFilename et on suppr les espaces
                $safeFilename = $slugger->slug($originalFilename);
                // pour éviter que les fichiers uploadés portant le même nom soient écrasés
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                // on déplace l'image du dossier temporaire (de wamp) à l'emplacement défini dans le services.yaml (parameters) et on lui donne le nom $newFilename
                try {
                    $image->move(
                        $this->getParameter('upload_image_product'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $productPicture = new ProductPicture();
                // on définit le path de la nouvell image
                $productPicture->setPath('uploads/products/'.$newFilename);
                // on définit le libele de la nouvelle image
                $productPicture->setLibele($originalFilename);
                // on ajoute la productPicture au produit renseigné dans formulaire
                $product->addProductPicture($productPicture);
            }
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('app_admin_product_all');
        }

        return $this->render('admin_product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
