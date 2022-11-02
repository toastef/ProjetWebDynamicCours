<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PaintingRepository;
use App\Repository\StyleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
public function paint(Painting $paints,CommentRepository $comment , Request $request, EntityManagerInterface $manager): Response
{
    $comments = $comment->findBy(
        ['isPubliched'=> true],[],
    );
    $commentaire = new Comment();
    $form = $this->createForm(CommentType::class , $commentaire);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {
        $commentaire->setIsPubliched(false);
        $manager->persist($commentaire);
        $paints->setComment($commentaire);
        $manager->flush();

        return $this->redirectToRoute('paint',['slug' => $paints->getSlug()]);
    }
    return $this->render('painting/detailPaint.html.twig', [
        'paint' => $paints,
        'form'  => $form->createView(),
        'comments' => $comments,
    ]);
}


}