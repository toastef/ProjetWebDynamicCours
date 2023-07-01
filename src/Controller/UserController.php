<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Entity\Tutoriel;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\ForgotPasswordRequestType;
use App\Form\PaintType;
use App\Repository\PaintingRepository;
use App\Repository\TutorielRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    /**
     * Variable Utilisé dans section profile
     * @param PaintingRepository $repository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/profile', name: 'app_user')]
    public function user(PaintingRepository $repository, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $repository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;

        }

        $userId = $this->getUser();
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $paintings = $repository->findLikedByUser($userId);
        $seller = $repository->findTablesBySellerRole($userId);
        $tutos = $user->getTutorielsSuivis();
        return $this->render('user/profile.html.twig', [
            'paintings' => $paintings,
            'vendeur' => $seller,
            'items' => $panierWithData,
            'total' => $total,
            'tutos' => $tutos,
        ]);
    }

    /**
     * Modification profile
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    #[Route('/user/edit-profile', name: 'app_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('app_user');
        }
        return $this->renderForm('user/editProfile.html.twig',
            [
                'form' => $form,
            ]);
    }


    /**
     * Ajout d'un element au panier
     * @param $id
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    #[Route('/user/add/{id}', name: 'paint_add')]
    public function add($id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('paintings');
    }


    /**
     * Suppression d'un element du panier
     * @param $id
     * @param SessionInterface $session
     * @return JsonResponse
     */
    #[Route('/user/remove/{id}', name: 'paint_remove')]
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return new JsonResponse(['success' => true]);
    }


    /**
     * Ajouter la mention Vendu a un tableau
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/vendeur/vendu/{id}', name: 'app_seller_vendu')]
    public function delete(Painting $painting, EntityManagerInterface $manager): Response
    {
        $painting->setVendu(!$painting->isVendu());
        $manager->flush();
        return $this->redirectToRoute('app_user');
    }


    /**
     * Ajout d'une peinture par l'utilisateur avec envoi de l'info par mail a tous les utilisateurs
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/vendeur/new', name: 'app_seller_new')]
    public function newPainting(Request $request, EntityManagerInterface $manager, Security $security, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        $user = $security->getUser();
        $roles = $user->getRoles();

        if(!in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_ADMIN",$roles) ){
            $user->setRoles(['ROLE_SELLER']);
            $manager->persist($user);
        }

        //Nouvelle peinture
        $paint = new Painting();
        $form = $this->createForm(PaintType::class, $paint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paint->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setImageName('image')
                ->setVendu(false)
                ->setVendeur($user)
                ->setSelected(false)
                ->setPublished(true)
                ->createSlug();
            $manager->persist($paint);
            $manager->flush();
            $this->addFlash('success', 'Tableau enregistré avec succes!');

            // email
            $userse = $userRepository->findAll();
            foreach ($userse as $users) {
                $email = (new TemplatedEmail())
                    ->from('info@Gallery.be')
                    ->to($users->getEmail())
                    ->subject('Nouveau tableau ajouté')
                    ->htmlTemplate('contact/new_painting.html.twig',)
                    ->context([
                        'title' => "Nouveau tableau ajouté à notre Gallery",
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'paint' => $paint->getImageName(),
                        'titreOeuvre' => $paint->getTitle(),
                        'prix' => $paint->getPrice(),
                    ]);
                $mailer->send($email);
            }
            return $this->redirectToRoute('paint', ['slug' => $paint->getSlug()]);
        }
        return $this->renderForm('painting/new.html.twig', [
            'form' => $form
        ]);
    }



    //Generate pdf----------------------------------------

    /**
     * @param SessionInterface $session
     * @param PaintingRepository $repository
     * @return void
     */
    #[Route('user/facture', name: 'app_facture')]
    public function generatePDF(SessionInterface $session, PaintingRepository $repository): Response
    {
        $path = realpath('../public/img/favicon.png');
        $image = file_get_contents($path);
        $base64Image = 'data:image/png;base64,' . base64_encode($image);
        $dompdf = new Dompdf();
        $date = new \DateTimeImmutable();
        $formattedDate = $date->format('d/m/Y');
        $panier = $session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $repository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;

        }
        $html = ($this->renderView('user/facture.html.twig', [
            'items' => $panierWithData,
            'total' => $total,
            'date' => $formattedDate,
            'base64Image' => $base64Image,
        ]));

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        /*dd($dompdf);*/
        // (Optionnel) Configurer les options de rendu
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF

        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', 'facture.pdf'),
        ]);

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

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @param PaintingRepository $repository
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/profile/contactvendeur', name: 'contact_vendeur')]
    public function contactVendeur(Request $request,MailerInterface $mailer, PaintingRepository $repository,  LoggerInterface $logger): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = $this->getUser();
        $id = $request->request->get('paint_id');
        $message = $request->request->get('message');
        $paint= $repository->find($id);

        try {
            $email = (new Email())
                ->from($user->getEmail())
                ->to($paint->getVendeur()->getEmail())
                ->subject('Contact pour informations peinture : '.$paint->getTitle())
                ->text($message);
            $mailer->send($email);
            $this->addFlash('success', 'Votre message a été transmit avec success');
            return $this->redirectToRoute('app_user');
        } catch (TransportExceptionInterface $e) {
            $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            $this->addFlash('success', 'Votre message a été transmit avec success');
            return $this->redirectToRoute('app_user');
        }
    }

    /**
     * @param $id
     * @param EntityManagerInterface $manager
     * @param TutorielRepository $tutorialRepo
     * @param Security $security
     * @return JsonResponse
     */
    #[Route('/tutoriel/{id}/remove', name: 'tuto_remove')]
    public function removetuto($id, EntityManagerInterface $manager, TutorielRepository $tutorialRepo,Security $security)
    {
        $user = $security->getUser();
        $tutoriel = $tutorialRepo->find($id);
        if ($tutoriel && $user->getTutorielsSuivis()->contains($tutoriel)) {
            $user->removeTutorielsSuivi($tutoriel);
            $manager->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }

}


