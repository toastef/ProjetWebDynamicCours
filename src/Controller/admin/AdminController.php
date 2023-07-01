<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Entity\TutoComment;
use App\Entity\Tutoriel;
use App\Entity\User;
use App\Form\PaintType;
use App\Form\TutorielType;
use App\Repository\CommentRepository;
use App\Repository\PaintingRepository;
use App\Repository\TutoCommentRepository;
use App\Repository\TutorielRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    /**
     * @param PaintingRepository $repository
     * @return Response
     */
    #[Route('/admin', name: 'app_admin_paint')]
    public function paint(PaintingRepository $repository, CommentRepository $comments): Response
    {

        $paint = $repository->findBy([],
            ['title' => 'ASC']);
        $commentaires = $comments->findAll();
        return $this->render('admin/paintings/paintings.html.twig', [
            'paints' => $paint,
            'comment' => $commentaires,

        ]);
    }


    /**
     * @param TutorielRepository $repository
     * @return Response
     */
    #[Route('/admin/tutos', name: 'app_admin_tutos')]
    public function tutos(TutorielRepository $repository, TutoCommentRepository $comments): Response
    {

        $tutos = $repository->findBy([],
            ['title' => 'ASC']);
        $commentaires = $comments->findAll();
        return $this->render('admin/tutoriels/tutoriels.html.twig', [
            'tutos' => $tutos,
            'comment' => $commentaires,

        ]);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/admin/new', name: 'app_admin_newTuto')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $tutoriel = new Tutoriel();
        $form = $this->createForm(TutorielType::class, $tutoriel);  // creation du formulaire
        $form->handleRequest($request);  // récupéeration des données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoriel = $form->getData();
            $style = $tutoriel->getStyle();
            $name = $style->getName();
            $i = null;
            switch ($name) {
                case 'Bombe':
                    $i = 1;
                    break;
                case 'Acrylique':
                    $i = 2;
                    break;
                case 'Pastel':
                    $i = 3;
                    break;
                case 'Aquarelle':
                    $i = 4;
                    break;
                case 'A l\'huile':
                    $i = 5;
                    break;
            }
            $tutoriel->setImage($i.'.jpg')
                        ->createSlug();
            $manager->persist($tutoriel);
            $manager->flush();
            $this->addFlash('success', 'Tutoriel enregistrée avec succes!');
            return $this->redirectToRoute('app_admin_tutos');
        }
        return $this->renderForm('admin/tutoriels/new.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @param Painting $paint
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/edit/{id}', name: 'app_admin_editTuto')]
    public function edit(Tutoriel $tutoriel, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(TutorielType::class, $tutoriel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tutoriel->createSlug();
            $manager->persist($tutoriel);
            $manager->flush();
            $this->addFlash('success', 'Tutoriel modifiée avec succès!');
            return $this->redirectToRoute('app_admin_tutos');
        }

        return $this->renderForm('admin/tutoriels/edit.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/hide/{id}', name: 'app_admin_hide')]
    public function delete(Painting $painting, EntityManagerInterface $manager, CommentRepository $commentaire): Response
    {
        $comments = $commentaire->findBy(['painting' => $painting]);
        foreach ($comments as $comment) {
            $manager->remove($comment);
        }
        $painting->setPublished(false); // Définit la propriété "published" sur false
        $manager->flush();
        $this->addFlash('success', 'Peinture masquée avec succès!');
        return $this->redirectToRoute('app_admin_paint');
    }

    /**
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    #[Route('admin/publish/{id}', name: 'app_admin_publish')]
    public function published(Comment $comment, EntityManagerInterface $manager)
    {

        $comment->setIsPubliched(!$comment->isIsPubliched());// set le contraire de ce qu'il récupère
        $manager->flush();
        $data = [
            'visibility' => $comment->isIsPubliched()
        ];

        return $this->json($data);
    }

    /**
     * @param TutoComment $tutoComment
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    #[Route('admin/tutoCom/publish/{id}', name: 'app_admin_tutoComment')]
    public function publishedTutoComs(TutoComment $tutoComment, EntityManagerInterface $manager)
    {

        $tutoComment->setIsPublished(!$tutoComment->isIsPublished());
        $manager->flush();
        $data = [
            'visibility' => $tutoComment->isIsPublished()
        ];

        return $this->json($data);
    }


    /**
     * @param Painting $painting
     * @param CommentRepository $comments
     * @return Response
     */
    #[Route('admin/comment/{id}', name: 'app_admin_comment')]
    public function viewComment(Painting $painting, CommentRepository $comments): Response
    {

        return $this->render('admin/paintings/Seecoms.html.twig', [
            'paints' => $painting,
            'comments' => $comments,

        ]);

    }

    /**
     * @param Tutoriel $tutoriel
     * @param TutoCommentRepository $comments
     * @param TutoComment $tutoComment
     * @return Response
     */
    #[Route('admin/tutoCom/{id}', name: 'app_admin_commentTuto')]
    public function viewTutoComment(Tutoriel $tutoriel, TutoCommentRepository $comments, UserRepository $user, Request $request): Response
    {

        return $this->render('admin/tutoriels/Seecoms.html.twig', [
            'tutos' => $tutoriel,
            'comments' => $comments,

        ]);

    }

    /**
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/admin/delecom/{id}', name: 'app_admin_delecom')]
    public function delcom(Comment $comment, EntityManagerInterface $manager, Request $request)
    {
        $route = $request->headers->get('referer');
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'Commentaire supprimé !');
        return $this->redirect($route);
    }

    // userPart
    #[Route('/admin/user', name: 'app_admin_user')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function adminUser(UserRepository $repository): Response
    {
        $user = $repository->findBy(
            [], // where
            ['createdAt' => 'DESC'],
        );

        return $this->render('admin/users/users.html.twig', [
            'users' => $user,
        ]);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param MailerInterface $mailer
     * @return JsonResponse
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/admin/user/{id}', name: 'app_admin_hideUser')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function adminHideUser(User $user, EntityManagerInterface $manager,MailerInterface $mailer)
    {
        $user->setSuspendu(!$user->isSuspendu());
        $manager->flush();
        $email = (new TemplatedEmail())
            ->from('info@Gallery.be')
            ->to($user->getEmail())
            ->subject('Suspension du compte')
            ->htmlTemplate('contact/suspension.html.twig',)
            ->context([
                'title' => "Suspension de votre compte",
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),

            ]);
        $mailer->send($email);
        $data = [
            'visibility' => $user->isSuspendu()
        ];

        return $this->json($data);
    }


    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Painting $painting
     * @param $selected
     * @return JsonResponse
     */
    #[Route('admin/slider/{id}', name: 'app_admin_slider')]
    public function slider( EntityManagerInterface $manager, Request $request, Painting $painting)
    {

        $painting->setSelected(!$painting->isSelected());

        // Enregistrer les modifications dans la base de données
        $manager->persist($painting);
        $manager->flush();

        $data = [
            'selected' => $painting->isSelected()
        ];


        return $this->json($data);
    }

}
