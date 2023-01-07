<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Form\EditProfileType;;
use App\Form\PaintType;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/profile', name: 'app_user')]
    public function user(PaintingRepository $repository): Response
    {
        $userId = $this->getUser();
        $paintings = $repository->findLikedByUser($userId);
        $seller = $repository->findTablesBySellerRole($userId);
        return $this->render('user/profile.html.twig', [
            'paintings' => $paintings,
            'vendeur' => $seller,
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
    public function add($id, SessionInterface $session) : Response
    {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1 ;
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('user_panier');

    }


        /**
         * @param PaintingRepository $paintingRepository
         * @param SessionInterface $session
         * @return Response
         */
    #[Route('/panier', name: 'user_panier')]
     public function panier(PaintingRepository $paintingRepository,SessionInterface $session)
     {
         $panier = $session->get('panier', []);
         $panierWithData = [];

         foreach ($panier as $id => $quantity){
             $panierWithData[] = [
                 'product' => $paintingRepository->find($id),
                 'quantity' => $quantity
             ];
         }
         $total = 0 ;
         foreach($panierWithData as $item){
             $totalItem = $item['product']->getPrice() * $item['quantity'];
             $total += $totalItem;

         }

         return $this->render('user/panier.hml.twig', [
             'items' => $panierWithData,
             'total' => $total,
         ]);
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

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('user_panier');
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
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $paint = new Painting();
        $form = $this->createForm(PaintType::class, $paint);  // creation du formulaire
        $form->handleRequest($request);  // récupéeration des données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $paint->setCreatedAt(new \DateTimeImmutable())
                ->setImageName('image')
                ->createSlug();
            $manager->persist($paint);
            $manager->flush();
            $this->addFlash('success', 'Oeuvre enregistrée avec succes!');
            return $this->redirectToRoute('app_admin_paint');
        }
        return $this->renderForm('admin/paintings/new.html.twig', [
            'form' => $form
        ]);
    }

}


