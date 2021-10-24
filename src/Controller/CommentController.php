<?php

namespace App\Controller;

use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class CommentController extends AbstractController
{
    /**
     * @Route("/mes-commentaires", name="my-comment")
     */
    public function myComment(): Response
    {
        $user = $this->getUser();
        return $this->render('comment/my-comment.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/comment/delete-comment/{id}", name="deleteComment")
     * @Method({"POST"})
     */
    public function deleteComment(Request $request, Comment $comment, EntityManagerInterface $manager) :Response
    {
        //On décode les données
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('deleteComment' . $comment->getId(), $data['_token'])){

            // On remove dans la BDD
            $manager->remove($comment);

            $manager->flush();
            // Reponse AJAX
            $return['success'] = true;
            return new JsonResponse($return);
        }else{
            // Reponse AJAX
            $return['success'] = false;
            return new JsonResponse($return);
        }
    }

    /**
     * @Route("/comment/update-comment/{id}", name="updateComment")
     * @Method({"UPDATE"})
     */
     public function updateComment(Request $request, Comment $comment, EntityManagerInterface $manager) :Response
     {
        //On décode les données
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('updateComment' . $comment->getId(), $data['_token'])){

            // Change la value du commentaire
            $comment->setValue($data['value']);

            // On fait persister
            $manager->persist($comment);
            $manager->flush();

            // Reponse AJAX
            $return['success'] = true;
            return new JsonResponse($return);
        }else{
            // Reponse AJAX
            $return['success'] = false;
            return new JsonResponse($return);
        }
    }
}