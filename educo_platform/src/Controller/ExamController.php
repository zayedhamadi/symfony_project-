<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Form\ExamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    #[Route('/exam/new', name: 'exam_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Exam object
        $exam = new Exam();

        // Create the form
        $form = $this->createForm(ExamType::class, $exam);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the exam to the database
            $entityManager->persist($exam);
            $entityManager->flush();

            // Redirect to a success page or show a flash message
            $this->addFlash('success', 'Exam added successfully!');
            return $this->redirectToRoute('exam_new');
        }

        // Render the form
        return $this->render('exam/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}