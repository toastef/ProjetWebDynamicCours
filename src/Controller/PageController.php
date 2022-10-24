<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{


    #[Route('/about', name: 'about')]
    public function about(){
        return $this->render('page/About.html.twig');
    }


    #[Route('/team', name: 'team')]
    public function team()
    {

        return $this->render('page/Team.html.twig');

    }

}
