<?php
namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Eleve;

use App\Entity\Note;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;

#[Route('/parent/quiz')]
class ParentQuizController extends AbstractController
{
    #[Route('/', name: 'parent_quiz_list')]
    public function list(QuizRepository $quizRepository): Response
    {
        // Fetch all quizzes
        $quizzes = $quizRepository->findAll();

        return $this->render('parent/quiz_list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/{id}', name: 'parent_take_quiz')]
    public function takeQuiz(Quiz $quiz, Request $request, SessionInterface $session): Response
    {
        // Create form for quiz questions
        $form = $this->createFormBuilder();

        foreach ($quiz->getQuestions() as $question) {
            $form->add('question_' . $question->getId(), ChoiceType::class, [
                'label' => $question->getTexte(),
                'choices' => array_combine($question->getOptions(), $question->getOptions()),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ]);
        }

        $form->add('submit', SubmitType::class, ['label' => 'Soumettre']);
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save form data in the session for later processing
            $session->set('quiz_results', $form->getData());

            // Redirect to submit the quiz
            return $this->redirectToRoute('parent_submit_quiz', [
                'id' => $quiz->getId(),
            ]);
        }

        return $this->render('parent/take_quiz.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/submit', name: 'parent_submit_quiz')]
    public function submitQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    // Get the quiz results stored in the session
    $data = $session->get('quiz_results');

    if (!$data) {
        // Handle case where session data is missing
        return $this->redirectToRoute('parent_quiz_list');
    }

    $score = 0;
    $totalQuestions = count($quiz->getQuestions());

    // Calculate score based on the answers
    foreach ($quiz->getQuestions() as $question) {
        if (isset($data['question_' . $question->getId()]) &&
            $data['question_' . $question->getId()] === $question->getReponse()) {
            $score++;
        }
    }

    // Create a dummy Eleve entity for testing
    $eleve = new Eleve();
    $eleve->setNom('Test Student');
    $eleve->setPrenom('Test Student'); // Set minimal attributes for testing
    $eleve->setDateDeNaissance(new \DateTime()); // Dummy date
    $eleve->setDateInscription(new \DateTime()); // Dummy date

    // Create a new Note for this quiz with the calculated score
    $note = new Note();
    $note->setQuiz($quiz);
    $note->setScore(($score / $totalQuestions) * 20); // Calculate score out of 20
    $note->setEleve($eleve); // Associate with the dummy Eleve

    // Persist the note
    $entityManager->persist($note);
    $entityManager->flush();

    // Clear the session data
    $session->remove('quiz_results');

    // Redirect to the result page after submitting the quiz
    return $this->redirectToRoute('parent_quiz_result', ['id' => $note->getId()]);
}
#[Route('/result/{id}', name: 'parent_quiz_result')]

public function quizResult(int $id, EntityManagerInterface $entityManager): Response
{
    // Retrieve the note entity
    $note = $entityManager->getRepository(Note::class)->find($id);

    if (!$note) {
        throw $this->createNotFoundException('Note not found.');
    }

    // Render the result page
    return $this->render('parent/quiz_result.html.twig', [
        'note' => $note,
    ]);
}

#[Route('/result/{id}/pdf', name: 'parent_quiz_result_pdf')]
public function quizResultPdf(int $id, EntityManagerInterface $entityManager, Pdf $knpSnappyPdf): Response
{
    // Retrieve the note entity
    $note = $entityManager->getRepository(Note::class)->find($id);

    if (!$note) {
        throw $this->createNotFoundException('Note not found.');
    }

    // Render the Twig template to HTML
    $html = $this->renderView('parent/quiz_result_pdf.html.twig', [
        'note' => $note,
    ]);

    // Generate the PDF
    $pdf = $knpSnappyPdf->getOutputFromHtml($html);

    // Return the PDF as a response
    return new PdfResponse(
        $pdf,
        'quiz-result.pdf' // Name of the downloaded file
    );
}
}
