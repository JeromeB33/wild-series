<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Program;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\Actor;

/**
* @Route("/actors", name="actor_")
*/

class ActorController extends AbstractController
{
    /**
     * @Route("/show/{actor}", name="show")
     */
    public function showActor(Actor $actor):Response
    {
        return $this->render('actor/actor_show.html.twig', ['actor' => $actor]);
    }
}
