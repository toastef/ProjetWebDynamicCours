<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Entity\TutoComment;
use App\Entity\Tutoriel;
use App\Entity\User;
use App\Form\CommentTutoType;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\StyleRepository;
use App\Repository\TutoCommentRepository;
use App\Repository\TutorielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorielController extends AbstractController
{
    #[Route('/tutoriel', name: 'app_tutoriel')]

    public function index(StyleRepository $styleRepository, TutorielRepository $tutorielRepository): Response
    {
        $style = $styleRepository->findAll();
        $tutorials = $tutorielRepository->findAll();

        return $this->render('tutoriel/tutoriels.html.twig', [
            'tutorials' => $tutorials,
            'categories' => $style,
        ]);
    }


    #[Route('/tutoriel/{slug}', name: 'tuto')]
    public function tuto(Tutoriel $tutoriel, TutoCommentRepository $comment, TutorielRepository $tutorielRepository, Request $request, EntityManagerInterface $manager, StyleRepository $styles): Response
    {
        try {
            $style = $styles->find($tutoriel->getId());
            $comments = $comment->findBy(
                ['is_published' => true],
            );
            $tutorials = $tutorielRepository->findAll();
            $commentaire = new TutoComment();
            $form = $this->createForm(CommentTutoType::class, $commentaire);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $commentaire->setIsPublished(false)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setUser($this->getUser())
                    ->setTutorial($tutoriel);
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
                'tutoriels' => $tutorials

            ]);
        } catch (\Exception $e) {
            return new Response('Une erreur s\'est produite lors de l\'affichage du tutoriel', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/tutoriel/{slug}/add', name: 'tutosU')]
    public function addtuto(Request $request, EntityManagerInterface $entityManager, Tutoriel $tutoriel)
    {
        try {
            $user = $entityManager->getRepository(User::class)->find($this->getUser());

            $tutorielsSuivis = $user->getTutorielsSuivis();

            if (!$tutorielsSuivis->contains($tutoriel)) {
                $user->addTutorielsSuivi($tutoriel);
                $entityManager->persist($user);
                $entityManager->flush();

                return new JsonResponse(['message' => 'Tutoriel ajouté avec succès']);
            }

            return new JsonResponse(['message' => 'Le tutoriel est déjà suivi']);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur s\'est produite lors de l\'ajout du tutoriel'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}
