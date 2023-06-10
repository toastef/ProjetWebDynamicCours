<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Entity\TutoComment;
use App\Entity\Tutoriel;
use App\Form\CommentTutoType;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\StyleRepository;
use App\Repository\TutoCommentRepository;
use App\Repository\TutorielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorielController extends AbstractController
{
    #[Route('/tutoriel', name: 'app_tutoriel')]
    public function index(StyleRepository $styleRepository,TutorielRepository $tutorielRepository ): Response
    {
        $style = $styleRepository->findAll();
        $tutorials = $tutorielRepository->findAll();

        return $this->render('tutoriel/tutoriels.html.twig', [
            'tutorials' => $tutorials,
            'categories' => $style,
        ]);
    }


    #[Route('/tutoriel/{slug}', name: 'tuto')]
    public function tuto(Tutoriel $tutoriel, TutoCommentRepository $comment, Request $request, EntityManagerInterface $manager, StyleRepository $styles): Response
    {
        $style = $styles->find($tutoriel->getId());
        $comments = $comment->findBy(
            ['is_published' => true],
        );

        $commentaire = new TutoComment();
        $form = $this->createForm(CommentTutoType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setIsPublished(false)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUserId($this->getUser())
                ->setTutorialId($tutoriel);
            $manager->persist($commentaire);
            $manager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été envoyé il sera validé par l\'administration');
            return $this->redirectToRoute('tuto', ['slug' => $tutoriel->getSlug()]);
        }
        return $this->render('tutoriel/detailTuto.html.twig', [
            'tuto' => $tutoriel,
            'styles' => $style,
            'form' => $form->createView(),
            'comments' => $comments,

        ]);
    }

}
