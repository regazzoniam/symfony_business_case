<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressType;
use App\Form\ProfilType;
use App\Repository\AdressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private AdressRepository $adressRepository , private EntityManagerInterface $em){ }

    #[Route('/', name: 'app_profil_index')]
    public function index(): Response
    {
        $user = $this->getUser() ;

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/commands', name: 'app_profil_commands')]
    public function showCommand(): Response
    {
        $user = $this->getUser() ;

        return $this->render('profil/showCommands.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_profil_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser() ;

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/adresses', name: 'app_profil_adresses')]
    public function showAdresses(): Response
    {
        $user = $this->getUser() ;

        return $this->render('profil/showAdresses.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/adresses/delete/{id}', name: 'app_profil_adresses_delete')]
    public function adressesDelete($id): Response
    {
        $adress = $this->adressRepository->find($id) ;
        $this->em->remove($adress);
        $this->em->flush();

        return $this->redirectToRoute('app_profil_adresses');
    }
    #[Route('/adresses/add', name: 'app_profil_adresses_add')]
    public function adressesAdd(Request $request): Response
    {
        $user = $this->getUser() ;
        $adress = new Adress();
        $adress->addUser($user);
        //set user avec get user

        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $adress = $form->getData();
            $this->em->persist($adress);
            $this->em->flush();
            return $this->redirectToRoute('app_profil_adresses');
        }

        return $this->render('profil/adresses_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
