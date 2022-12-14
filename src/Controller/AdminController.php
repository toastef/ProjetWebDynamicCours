<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Form\PaintType;
use App\Repository\CommentRepository;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    /**
     * @param PaintingRepository $repository
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function paint(PaintingRepository $repository, CommentRepository $comments): Response
    {

        $paint = $repository->findBy([],
            ['title' => 'ASC']);
        $commentaires = $comments->findAll();
        return $this->render('admin/admin.html.twig', [
            'paints' => $paint,
            'comment' => $commentaires,

        ]);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/admin/new', name: 'new')]
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
            return $this->redirectToRoute('admin');
        }
        return $this->renderForm('admin/new.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @param Painting $paint
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/edit/{id}', name: 'edit')]
    public function edit(Painting $paint, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(PaintType::class, $paint);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paint->createSlug();
            $manager->flush();
            $this->addFlash('success', 'Peinture modifiée avec succès!');
            return $this->redirectToRoute('admin');
        }

        return $this->renderForm('admin/edit.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/delete/{id}', name: 'delete')]
    public function delete(Painting $painting, EntityManagerInterface $manager, CommentRepository $commentaire): Response
    {
        $comments = $commentaire->findBy(['painting' => $painting]);
        foreach ($comments as $comment) {
            $manager->remove($comment);
        }
        $manager->remove($painting);
        $manager->flush();
        return $this->redirectToRoute('admin');
    }

    /**
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('admin/publish/{id}', name: 'publish')]
    public function published(Comment $comment, EntityManagerInterface $manager)
    {
        $comment->setIsPubliched(!$comment->isIsPubliched());// set le contraire de ce qu'il récupère
        $manager->flush();
        return $this->redirectToRoute('admin',);
    }


    /**
     * @param Painting $painting
     * @param CommentRepository $comments
     * @return Response
     */
    #[Route('admin/comment/{id}', name: 'comment')]
    public function viewComment(Painting $painting, CommentRepository $comments): Response
    {

        return $this->render('admin/Seecoms.html.twig', [
            'paints' => $painting,
            'comments' => $comments,

        ]);

    }

    /**
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/admin/delecom/{id}', name: 'delecom')]
    public function delcom(Comment $comment, EntityManagerInterface $manager, Request $request)
    {
        $route = $request->headers->get('referer');
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'Commentaire supprimé !');
        return $this->redirect($route);
    }

}
