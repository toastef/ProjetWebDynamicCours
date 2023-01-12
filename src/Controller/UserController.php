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
     * @param $id
     * @param SessionInterface $session
     * @return void
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
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/vendeur/vendu/{id}', name: 'app_seller_vendu')]
    public function delete(Painting $painting, EntityManagerInterface $manager): Response
    {
        $painting->setVendu(!$painting->isVendu());// set le contraire de ce qu'il récupère
        $manager->flush();
        return $this->redirectToRoute('app_user');
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/vendeur/new', name: 'app_seller_new')]
    public function newPainting(Request $request, EntityManagerInterface $manager, Security $security, MailerInterface $mailer, UserRepository $userRepository ): Response
    {
        $user = $security->getUser();
        // Mise à jour du rôle de l'utilisateur
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
            $this->addFlash('success', 'Oeuvre enregistrée avec succes!');

                // email
            $userse = $userRepository->findAll();
            foreach ($userse as $users) {
                $email = (new TemplatedEmail())
                    ->from('info@Gallery.be')
                    ->to( 'info@gall.be')
                    ->subject('Nouveau tableau ajouté')
                    ->htmlTemplate('contact/new_painting.html.twig',)
                    ->context([
                        'title'=> "Nouveau tableau Ajouté a notre Gallery",
                        'firstName' => $user->getFirstName(),
                        'lastName'  => $user->getLastName(),
                        'paint' => $paint->getImageName(),
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


