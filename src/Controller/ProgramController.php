<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\SearchProgramType;
use App\Service\Slugify;
use App\Form\ProgramType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
* @Route("/programs", name="program_")
*/

class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }
    
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * The controller for the program add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer ) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
                // Get data from HTTP request
        $form->handleRequest($request);

        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Program Object
            $entityManager->persist($program);
            $program->setOwner($this->getUser());
            // Flush the persisted object
            $entityManager->flush();
            $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('9d634b4a41-58996a@inbox.mailtrap.io')
            ->subject('Une nouvelle série vient d\'être publiée !')
            ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);
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
     * @param Program $program
     * @param Season $season
     * @Route("/{slug}/season/{season}", name="season_show")
     */
    public function showSeason(Program $program, Season $season):Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program]);
    }

    /**
     * @param Program $program
     * @param Season $season
     * @param Episode $episode
     * @return Response
     * @Route("/{slug}/season/{season}/episode/{episode}", name="episode_show")
     */     
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request):Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //recup the entity manager
            $entityManager = $this->getDoctrine()->getManager();

            /** @var \App\Entity\User $author */
            $author = $this->getUser();

            $comment->setAuthor($author);
            $comment->setEpisode($episode);
            // persist anf flush : ajout dans la base
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        return $this->render('program/episode_show.html.twig', ['episode' => $episode, 'season' => $season, 'program' => $program, 'form' => $form->createView()]);
    }

    /**
     * @Route("/programs/{slug}/edit", name="edit")
     */
    public function edit(Request $request, Program $program): Response
    {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView()
        ]);
    }
}
