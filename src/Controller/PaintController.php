<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Repository\PaintingRepository;
use App\Repository\StyleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaintController extends AbstractController
{
    /**
     * @param PaintingRepository $paintingRepository
     * @param StyleRepository $styleRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/paintings', name: 'paintings')]
    public function painting(PaintingRepository $paintingRepository, StyleRepository $styleRepository) : Response
    {
        $styles = $styleRepository->findBy(
            [],
            ['name' => 'ASC']
        );
        $paints = $paintingRepository->findBy( [],
            ['title' => 'ASC']
        );

        return $this->render('painting/painting.html.twig' , [
            'styles' => $styles,
            'paints' => $paints
        ]);
    }

#[Route('/painting/{slug}', name: 'paint')]
public function course(Painting $paints): Response
{
    return $this->render('painting/detailPaint.html.twig', [
        'paint' => $paints
    ]);
}


}