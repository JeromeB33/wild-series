<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;

/**
* @Route("/categories", name="category_")
*/

class CategoryController extends AbstractController
{
        /**
     * Show all rows from Category’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
         $categories = $this->getDoctrine()
             ->getRepository(Category::class)
             ->findAll();

         return $this->render(
             'category/index.html.twig',
             ['categories' => $categories]
         );
    }

        /**
     * Getting programs by category
     *
     * @Route("/show/{categoryName}", name="show")
     * @return Response
     */
    public function show(string $categoryName):Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        if (!$category) {
            $error = "No existing category named : " . $categoryName ;
            return $this->render('error404.html.twig', ['error' => $error]);
        }

        $categoryPrograms = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()], ['id' => 'DESC'], 3 );

        return $this->render('category/show.html.twig', [
            'programs' => $categoryPrograms, 'category' => $category
        ]);
    }
}
