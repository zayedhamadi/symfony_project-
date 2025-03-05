<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Cours;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/course/{id}/add-comment', name: 'course_add_comment', methods: ['POST'])]
    public function addComment(Cours $course, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Get the comment from the request
        $commentContent = $request->request->get('comment');

        if (empty($commentContent)) {
            return new JsonResponse(['success' => false, 'error' => 'Comment cannot be empty.']);
        }

        // Create a new comment
        $comment = new Comment();
        $comment->setContent($commentContent);
        $comment->setCourse($course);
        $comment->setCreatedAt(new \DateTime());

        // Save the comment
        $em->persist($comment);
        $em->flush();

        // Return a success response
        return new JsonResponse(['success' => true]);
    }

    #[Route('/course/{id}/view-comments', name: 'course_view_comments', methods: ['GET'])]
    public function viewComments(Cours $course): Response
    {
        // Fetch comments for the course
        $comments = $course->getComments();

        // Render the comments
        return $this->render('comment/view_comment.html.twig', [
            'course' => $course,
            'comments' => $comments,
        ]);
    }
}