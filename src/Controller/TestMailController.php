<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test/mail', name: 'app_test_mail')]
    public function index(MailerInterface $mailer): Response
    {
        $email = new Email();
        $email
            ->from('formationSymfony63@gmail.com')
            ->to('regadamandine@gmail.com')
            ->subject('Mon nouveau sujet')
            ->html('<p style="color: red">Hello</p>');
        
        $mailer->send($email);

        return $this->render('test_mail/index.html.twig', [
            'controller_name' => 'TestMailController',
        ]);
    }
}
