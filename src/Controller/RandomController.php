<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RandomController extends AbstractController
{
    /**
     * @Route("/random", name="app_random")
     */



    public function random(): Response
    {
        $number = rand(0, 100);
        //dd($number); //DD means dump and die, it kills the code after dumping the variable

        return $this->render('random/random.html.twig', [
            'number' => $number
        ]);
    }
}
