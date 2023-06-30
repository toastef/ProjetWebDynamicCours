<?php

namespace App\Controller;

use App\Repository\PaintingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @param PaintingRepository $paintingRepository
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function indexSlider(PaintingRepository $paintingRepository): Response
    {
        $paints = $paintingRepository->findby(
            ['selected' => true],
            [],
        );

        return $this->render('page/index.html.twig',
            [
                'paints' => $paints,
            ]);
    }


    /**
     * @param SessionInterface $session
     * @param PaintingRepository $paintingRepository
     * @return Response
     */
    #[Route('/panier', name: 'user_panier')]
    public function panier(SessionInterface $session, PaintingRepository $paintingRepository)
    {
        $panier = $session->get('panier', []);
        $total = count($panier);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $paintingRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $totalachat = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $totalachat += $totalItem;

        }
        return $this->render('partials/nav.html.twig',
            [
                'total' => $total,
                'items' => $panierWithData,
                'totalAchat' => $totalachat,
            ]);
    }



}