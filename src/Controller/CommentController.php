<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/comment", name="comment_")
 */

class CommentController extends AbstractController
{
    /**
     * @param Comment $comment
     * @param Request $request
     * @return Response
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Comment $comment, Request $request): Response
    {
        if($this->isGranted('ROLE_ADMIN') === false){
            if(!($this->getUser() === $comment->getAuthor())){
                throw new AccessDeniedException('Only the owner or admin can delete the commentary!');
            }
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('danger', 'The comment has been deleted');

        }

        return $this->redirectToRoute('program_index');
    }
}
