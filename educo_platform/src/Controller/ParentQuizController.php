<?php
namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Eleve;
use App\Repository\CoursRepository; // Add this line
use App\Repository\ClasseRepository;
use App\Repository\UserRepository;

use App\Entity\Note;
use App\Repository\QuizRepository;
use App\Repository\NoteRepository;

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
    public function list(
        Request $request,
        QuizRepository $quizRepository,
        CoursRepository $coursRepository,
        ClasseRepository $classeRepository,
        NoteRepository $noteRepository,
        SessionInterface $session,UserRepository $userRepository  ,
        EntityManagerInterface $entityManager// Use NoteRepository
    ): Response {
        // Récupérer les paramètres de filtrage et de recherche
        $coursId = $request->query->get('cours');
        $classeId = $request->query->get('classe');
        $search = $request->query->get('search');
    
        // Convertir les IDs en entiers (ou null si vide)
        $coursId = $coursId !== null && $coursId !== '' ? (int)$coursId : null;
        $classeId = $classeId !== null && $classeId !== '' ? (int)$classeId : null;
    
        // Récupérer la liste des cours et classes pour le formulaire de filtrage
        $coursList = $coursRepository->findAll();
        $classesList = $classeRepository->findAll();
    
        // Filtrer les quizzes en fonction des paramètres
        $quizzes = $quizRepository->findByFilters($coursId, $classeId, $search);
    
        // Récupérer l'utilisateur connecté (parent) et son enfant
        $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);
        dump($user);  // Check user
        
        // Fetch the 'Eleve' (child) for this parent
        $eleve = $entityManager->getRepository(Eleve::class)->findOneBy(['IdParent' => $user]);
        dump($eleve);  // Check eleve
        
        if (!$eleve) {
            throw $this->createNotFoundException('Aucun élève trouvé pour ce parent.');
        }

        // Fetch historical notes for the child
        $historique = $noteRepository->findByEleve($eleve);  // Use the method from NoteRepository
        dump($historique);  // Check historical results

        // Render the template with historical results and quiz list
        return $this->render('parent/quiz_list.html.twig', [
            'quizzes' => $quizzes,
            'coursList' => $coursList,
            'classesList' => $classesList,
            'selectedCours' => $coursId,
            'selectedClasse' => $classeId,
            'searchQuery' => $search,
            'historique' => $historique,  // Pass historical results
        ]);
    
}

    #[Route('/{id}', name: 'parent_take_quiz')]
   public function takeQuiz(Quiz $quiz, Request $request, SessionInterface $session): Response
{
    // Vérifier si l'heure de début est stockée ou si elle est expirée
    if (!$session->has('quiz_start_time') || (time() - $session->get('quiz_start_time')) >= 60) {
        $session->set('quiz_start_time', time()); // Stocker le timestamp actuel
    }

    // Définir la durée du quiz à 1 minute (60 secondes)
    $quizDuration = 60;

    // Calculer le temps restant
    $startTime = $session->get('quiz_start_time');
    $remainingTime = max(0, $quizDuration - (time() - $startTime));

    if ($remainingTime <= 0) {
        // Temps écoulé, rediriger vers la soumission automatique
        return $this->redirectToRoute('parent_submit_quiz', ['id' => $quiz->getId()]);
    }

    // Création du formulaire pour les questions du quiz
    $formBuilder = $this->createFormBuilder();
    
    // Convert the PersistentCollection to an array and shuffle it
    $questions = $quiz->getQuestions()->toArray(); // Convert to array
    shuffle($questions); // Randomize questions order

    foreach ($questions as $question) {
        // Shuffle answer options
        $options = $question->getOptions();
        shuffle($options); // Randomize answers order

        $formBuilder->add('question_' . $question->getId(), ChoiceType::class, [
            'label' => $question->getTexte(),
            'choices' => array_combine($options, $options),
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ]);
    }

    $formBuilder->add('submit', SubmitType::class, ['label' => 'Soumettre']);
    $form = $formBuilder->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $session->set('quiz_results', $form->getData());
        return $this->redirectToRoute('parent_submit_quiz', ['id' => $quiz->getId()]);
    }

    return $this->render('parent/take_quiz.html.twig', [
        'quiz' => $quiz,
        'form' => $form->createView(),
        'remainingTime' => $remainingTime, // Temps restant envoyé au template
    ]);
}
    #[Route('/{id}/submit', name: 'parent_submit_quiz')]
    public function submitQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager, SessionInterface $session,UserRepository $userRepository): Response

    {
        $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);

        $startTime = $session->get('quiz_start_time');
        $quizDuration = 1 * 60; // 10 minutes
    
        if (time() - $startTime > $quizDuration) {
            // Quiz expired, redirect without saving results
            $session->remove('quiz_results');
            return $this->redirectToRoute('parent_quiz_list');
        }
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

    $eleve = $entityManager->getRepository(Eleve::class)->findOneBy(['IdParent' => $user]);
    

    // Create a new Note for this quiz with the calculated score
    $note = new Note();
    $note->setQuiz($quiz);
    $note->setScore(($score / $totalQuestions) * 20); // Calculate score out of 20
    $note->setEleve($eleve);
     // Associate with the dummy Eleve

    // Persist the note
    $entityManager->persist($note);
    $entityManager->flush();

    // Clear the session data
    $session->remove('quiz_results');
    $session->remove('quiz_start_time'); // Clear start time


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
