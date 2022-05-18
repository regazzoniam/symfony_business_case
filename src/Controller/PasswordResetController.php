<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Form\EmailPasswordResetType;
use App\Form\PasswordResetType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    public function __construct (private ResetPasswordRepository $resetPasswordRepository, private UserRepository $userRepository, private EntityManagerInterface $em){}

    #[Route('/password/forgotten', name: 'app_password_forgotten')]
    public function forgotten(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(EmailPasswordResetType::class);
        $form->handleRequest($request); 


        if($form->isSubmitted() && $form->isValid()){
            $datas = $form->getData();
            dump($datas);
            $user = $this->userRepository->findOneBy(['email' => $datas['email']]);
            dump($user);

            if($user !== null){
                $token = uniqid();
                dump($token);
                $resetPassword = new ResetPassword;
                $resetPassword->setToken($token);
                $resetPassword->setUser($user);
                $resetPassword->setCreatedAt(new DateTime());
                $this->em->persist($resetPassword);
                $this->em->flush();

                $email = new Email();
                $email
                    ->from('formationSymfony63@gmail.com')
                    // user qui veut changer son mot de passe
                    ->to('regadamandine@gmail.com')
                    ->subject('La Nîmes\'alerie - Changer votre mot de passe')
                    ->html('
                    <p>
                    Bonjour  , <br>
                    pour modifier votre mot de passe , veuillez cliquer sur le lien.
                    <br>
                    <a href="http://127.0.0.1:8000/password/reset/'.$resetPassword->getToken().'">
                        Réinitialisation du mot de passe
                    </a>
                    </p>');
                
                $mailer->send($email);

                return $this->redirectToRoute('app_password_email_send');
            }
        }

        return $this->render('password_reset/forgotten.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/password/email_send', name: 'app_password_email_send')]
    public function emailSend(): Response
    {
        return $this->render('password_reset/email_send.html.twig', [
            
        ]);
    }

    #[Route('/password/reset/{token}', name: 'app_password_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, $token): Response
    {
        if ($token != null) {
            $form = $this->createForm(PasswordResetType::class);
            $form->handleRequest($request); 

            $resetPassword = $this->resetPasswordRepository->findOneBy(['token' => $token]);
            $user = $resetPassword->getUser();
            dump($user);

            if($form->isSubmitted() && $form->isValid()){
                $datas = $form->getData();
                dump($datas);
                $newpassword = $datas['password'];
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $newpassword
                    )
                );
                $this->em->persist($user);
                $this->em->flush();
                return $this->redirectToRoute('app_password_confirm');
            }
        }
        

        return $this->render('password_reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/password/confirm', name: 'app_password_confirm')]
    public function confirm(): Response
    {
        return $this->render('password_reset/confirm.html.twig', [
            
        ]);
    }

}

