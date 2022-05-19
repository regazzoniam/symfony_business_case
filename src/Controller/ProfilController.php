<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em){ }

    #[Route('/', name: 'app_profil_index')]
    public function index(Request $request): Response
    {
        $user = $this->getUser() ;

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/commands', name: 'app_profil_commands')]
    public function showCommand(Request $request): Response
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
}
