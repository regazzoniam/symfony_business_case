<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\CommandRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/command')]
class AdminCommandController extends AbstractController
{
    #[Route('/', name: 'app_admin_command_index', methods: ['GET'])]
    public function index(CommandRepository $commandRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $qb = $commandRepository->getQbAll();
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page',1),
            20
        );

        return $this->render('admin_command/index.html.twig', [
            'pagination' => $pagination,
        ]);
        // return $this->render('admin_command/index.html.twig', [
        //     'commands' => $commandRepository->findAll(),
        // ]);
    }

    #[Route('/new', name: 'app_admin_command_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandRepository $commandRepository): Response
    {
        $command = new Command();
        $command->setCreatedAt(new DateTime());
        $command->setNumCommand((int)uniqid());
        $command->setStatus(100);
        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandRepository->add($command);
            return $this->redirectToRoute('app_admin_command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_command/new.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_command_show', methods: ['GET'])]
    public function show(Command $command): Response
    {
        return $this->render('admin_command/show.html.twig', [
            'command' => $command,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_command_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Command $command, CommandRepository $commandRepository): Response
    {
        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandRepository->add($command);
            return $this->redirectToRoute('app_admin_command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_command/edit.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_command_delete', methods: ['POST'])]
    public function delete(Request $request, Command $command, CommandRepository $commandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
            $commandRepository->remove($command);
        }

        return $this->redirectToRoute('app_admin_command_index', [], Response::HTTP_SEE_OTHER);
    }
}
