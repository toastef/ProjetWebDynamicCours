<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function email(MailerInterface $mailer , Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $email = new Email();
            $email  ->from($contact->getEmail())
                ->to('info@Gallery.be')
                ->subject($contact->getSubject())
                ->html(
                    '
                        <p>'.$contact->getMessage().'</p>
                        <p><small>'. $contact->getFirstName().'</small></p>'
                );
            $mailer->send($email);
            $this->addFlash('success', 'Votre message a bien été envoyé Vous recevrez une réponse bientôt');
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
