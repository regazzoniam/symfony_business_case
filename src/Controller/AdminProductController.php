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

// CRUD réalisé manuellement

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private ProductRepository $productRepository, private PaginatorInterface $paginator)
    { }
// Voir tous les produits
    #[Route('/all', name: 'app_admin_product_index')]
    public function index(Request $request): Response
    {
        $qb = $this->productRepository->getQbAll();
        $pagination = $this->paginator->paginate(
            // 1er argument : query builder
            $qb,
            // 2eme argument : sur quel page se trouve (1: page par défaut)
            $request->query->getInt('page',1),
            // 3eme argument : nombre de résultats par page
            20
        );

        return $this->render('admin_product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
// Créer un nouveau produit
    #[Route('/new', name: 'app_admin_product_new')]
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
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin_product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
// Modifier un produit
    #[Route('/edit/{id}', name: 'app_admin_product_edit')]
    public function edit($id, Request $request): Response
    {
        $productEntity = $this->productRepository->findOneBy(['id' => $id]) ;

        $form = $this->createForm(ProductType::class, $productEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin_product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
// Voir en détail un produit
    #[Route('/show/{id}', name: 'app_admin_product_show')]
    public function show($id): Response
    {
        $productEntity = $this->productRepository->findOneBy(['id' => $id]) ;

        return $this->render('admin_product/show.html.twig', [
            'product' => $productEntity
        ]);
    }
// Supprimer un produit
    #[Route('/delete/{id}', name: 'app_admin_product_delete')]
    public function delete($id): Response
    {
        $productEntity = $this->productRepository->findOneBy(['id' => $id]) ;

        $this->em->remove($productEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_product_index');
    }
    
}
