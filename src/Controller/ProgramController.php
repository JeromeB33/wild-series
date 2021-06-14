<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Program;
use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramType;
use App\Service\Slugify;

/**
* @Route("/programs", name="program_")
*/

class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
         $programs = $this->getDoctrine()
             ->getRepository(Program::class)
             ->findAll();

         return $this->render(
             'program/index.html.twig',
             ['programs' => $programs]
         );
    }

    /**
     * The controller for the program add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify ) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
                // Get data from HTTP request
        $form->handleRequest($request);
        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Program Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        // Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @param Program $program
     * @return Response
     * @Route ("/{slug}", methods={"GET"}, name="show")
     */
    public function show(Program $program): Response
    {
        if(!$program){
            //throw $this->createNotFoundException()('No program with id : '.$id.' found in program\'s table.');
            $message = 'No program with id : '.$program.' found in program\'s table.';
            return $this->render('error404.html.twig', ['error' => $message]);
        }

        return $this->render('program/show.html.twig', ['program' => $program]);
    }
    /**
     * @Route("/{slug}/season/{season}", name="season_show")
     */
    public function showSeason(Program $program, Season $season):Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program]);
    }

    /**
     * @Route("/{slug}/season/{season}/episode/{episode}", name="episode_show")
     */     
    public function showEpisode(Program $program, Season $season, Episode $episode):Response
    {
        return $this->render('program/episode_show.html.twig', ['episode' => $episode, 'season' => $season, 'program' => $program]);
    }

}
