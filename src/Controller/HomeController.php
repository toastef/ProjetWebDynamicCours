<?php

namespace App\Controller;

use App\Repository\PaintingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PaintingRepository $paintingRepository): Response
    {
        $paintLimit = $paintingRepository->findBy(
            [],
            [],
            9
        );

        return $this->render('page/index.html.twig',
        [
            'paints' => $paintLimit,
        ]);
    }
}