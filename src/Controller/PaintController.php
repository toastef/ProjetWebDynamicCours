<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Entity\Style;
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
public function paint(Painting $paints,CommentRepository $comment , Request $request, EntityManagerInterface $manager,StyleRepository $styles): Response
{
    $style = $styles->find($paints->getId());
    $comments = $comment->findBy(
        ['isPubliched'=> true ],
    );
    $commentaire = new Comment();
    $form = $this->createForm(CommentType::class , $commentaire);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {
        $commentaire->setIsPubliched(false);
        $commentaire->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($commentaire);
        $paints->addComment($commentaire);
        $manager->flush();
        $this->addFlash('success', 'Votre commentaire a bien été envoyé il sera validé par l\'administration');
        return $this->redirectToRoute('paint',['slug' => $paints->getSlug()]);
    }
    return $this->render('painting/detailPaint.html.twig', [
        'paint' => $paints,
        'styles' => $style,
        'form'  => $form->createView(),
        'comments' => $comments,

    ]);
}


}