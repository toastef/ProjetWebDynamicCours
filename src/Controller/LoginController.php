<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordRequestType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/suspendu', name: 'user_suspended_route')]
    public function redirectSuspended(): Response
    {


        return $this->render('user/suspendu.html.twig',
            [

            ]);
    }

    /**
     * @return void
     */
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(SessionInterface $session)
    {
        $session->set('panier',[]);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer,UserRepository $repository): Response
    {
        $form = $this->createForm(ForgotPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEmail = $form->get('email')->getData();

            $user = $repository->findOneBy(['email' => $userEmail]);
            if ($user) {
                $email = (new TemplatedEmail())
                    ->from($userEmail)
                    ->to('info@art.be')
                    ->subject('Demande Password')
                    ->htmlTemplate('contact/forgotPass.html.twig')
                    ->context([
                        'title' => "Demande de password",
                        'firstName' => $user->getFirstName(),
                    ]);

                $mailer->send($email);

                $this->addFlash('success', 'Un e-mail de demande du mot de passe a été envoyé à l\'admin.');
                return $this->redirectToRoute('app_login');
            }else {

                $this->addFlash('danger', 'L\'adresse e-mail fournie n\'existe pas.');
            }


        }
        return $this->render('login/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
