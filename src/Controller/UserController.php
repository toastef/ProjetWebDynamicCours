<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Form\EditProfileType;
use App\Form\PaintType;
use App\Repository\PaintingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    /**
     * @param PaintingRepository $repository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/profile', name: 'app_user')]
    public function user(PaintingRepository $repository, SessionInterface $session): Response
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
        $paintings = $repository->findLikedByUser($userId);
        $seller = $repository->findTablesBySellerRole($userId);
        return $this->render('user/profile.html.twig', [
            'paintings' => $paintings,
            'vendeur' => $seller,
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

    /**
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
     * ajout d'un element au panier
     * @param $id
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    #[Route('/user/add/{id}', name: 'paint_add')]
    public function add($id, SessionInterface $session, Request $request): Response
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('paintings');
    }


    /**
     * Suppression d'un element du panier
     * @param $id
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/user/remove/{id}', name: 'paint_remove')]
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_user');
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
        // Mise ?? jour du r??le de l'utilisateur
        $user->setRoles(['ROLE_SELLER']);
        $manager->persist($user);
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
                ->createSlug();
            $manager->persist($paint);
            $manager->flush();
            $this->addFlash('success', 'Tableau enregistr?? avec succes!');

            // email
            $userse = $userRepository->findAll();
            foreach ($userse as $users) {
                $email = (new TemplatedEmail())
                    ->from('info@Gallery.be')
                    ->to($users->getEmail())
                    ->subject('Nouveau tableau ajout??')
                    ->htmlTemplate('contact/new_painting.html.twig',)
                    ->context([
                        'title' => "Nouveau tableau ajout?? ?? notre Gallery",
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'paint' => $paint->getImageName(),
                        'titreOeuvre' => $paint->getTitle(),
                        'prix' => $paint->getPrice(),
                    ]);
                $mailer->send($email);
            }
            return $this->redirectToRoute('app_user');
        }
        return $this->renderForm('painting/new.html.twig', [
            'form' => $form
        ]);
    }
}


