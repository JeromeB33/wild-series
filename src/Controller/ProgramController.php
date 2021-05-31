<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Episode;

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
     * Getting a program by id
     *
     * @Route("/show/{id<^[0-9]+$>}", methods={"GET"}, name="show")
     * @return Response
     */
    public function show(int $id):Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * @Route("/show/{program}/season/{season}", name="season_show")
     */
    public function showSeason(Program $program, Season $season):Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program]);
    }

    /**
     * @Route("/show/{program}/season/{season}/episode/{episode}", name="episode_show")
     */     
    public function showEpisode(Program $program, Season $season, Episode $episode):Response
    {
        return $this->render('program/episode_show.html.twig', ['episode' => $episode, 'season' => $season, 'program' => $program]);
    }

}
