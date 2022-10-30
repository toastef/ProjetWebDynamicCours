<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Form\PaintType;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Entity\File;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Naming\HashNamer;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function paint(PaintingRepository $repository): Response
    {
        $paint = $repository->findBy([],
            ['title' => 'ASC']);

        return $this->render('admin/admin.html.twig', [
            'paints' => $paint,
        ]);
    }

    #[Route('/admin/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $manager): Response // la classe request est envoi les infos au controller
    {

        $paint = new Painting();
        $form = $this->createForm(PaintType::class, $paint);  // creation du formulaire
        $form->handleRequest($request);  // récupéeration des données du formulaire

        if ($form->isSubmitted() && $form->isValid())
        {
            $paint->setCreatedAt(new \DateTimeImmutable())
                  ->setImageName('image')
                  ->createSlug();
            $manager->persist($paint);
            $manager->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->renderForm('admin/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'edit')]
    public function edit(Painting $paint, EntityManagerInterface $manager,Request $request): Response
    {
        $form= $this->createForm(PaintType::class, $paint);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $paint->createSlug();
            $manager->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->renderForm('admin/edit.html.twig', [
            'form'=>$form
        ]);
    }


    /**
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/delete/{id}', name: 'delete')]
    public function delete(Painting $painting, EntityManagerInterface $manager): Response
    {
        $manager->remove($painting);
        $manager->flush();
        return $this->redirectToRoute('admin');
    }


}
