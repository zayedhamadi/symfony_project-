<?php
namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_cours_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, CoursRepository $coursRepository): Response
    {
        // Get the logged-in user's ID from the session
        $session = $request->getSession();
        $userId = $session->get('user_id');

        // Fetch the logged-in user (enseignant)
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non connecté.');
        }

        // Fetch courses associated with the logged-in enseignant
        $cours = $coursRepository->findByEnseignant($userId);

        return $this->render('cours/index.html.twig', [
            'cours' => $cours, // Pass the filtered courses to the template
        ]);
    }
    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, MatiereRepository $matiereRepository): Response
    {
        // Get the user ID from the session
        $session = $request->getSession();
        $userId = $session->get('user_id');

        // Fetch matières related to the logged-in user using a custom query
        $matieres = $matiereRepository->findByUser($userId);

        // Create a new course
        $cour = new Cours();

        // Create the form and pass the filtered matières
        $form = $this->createForm(CoursType::class, $cour, [
            'matieres' => $matieres, // Pass the filtered matières to the form
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle PDF file upload
            $pdfFile = $form->get('pdfFile')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                    $cour->setPdfFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Could not upload file.');
                }
            }

            // Save the course
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index');
        }

        return $this->render('cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();
            if ($pdfFile) {
                // Generate a unique file name
                $newFilename = uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'), // Directory where PDFs are stored
                        $newFilename
                    );

                    // Delete old file if it exists
                    if ($cour->getPdfFilename()) {
                        $oldFilePath = $this->getParameter('pdf_directory').'/'.$cour->getPdfFilename();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath); // Remove old PDF
                        }
                    }

                    // Update the pdf filename in the entity
                    $cour->setPdfFilename($newFilename);
                } catch (FileException $e) {
                    // Handle file upload failure
                    $this->addFlash('error', 'Failed to upload file.');
                    return $this->redirectToRoute('app_cours_edit', ['id' => $cour->getId()]);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_cours_index');
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index');
    }

    #[Route('/student/courses', name: 'student_courses')]
    public function studentCourses(
        Request $request,
        CoursRepository $coursRepository,
        ClasseRepository $classeRepository
    ): Response {

        // Get all classes for the dropdown
        $classes = $classeRepository->findAll();

        // Get selected class from request
        $selectedClassId = $request->query->get('class_id');
        $courses = [];
        $groupedCourses = []; // Initialize groupedCourses here

        if ($selectedClassId) {
            $courses = $coursRepository->findBy(['classe' => $selectedClassId],
            ['chapterNumber' => 'ASC']);

            // Group courses by Matiere
//            $groupedCourses = [];
            foreach ($courses as $course) {
                $matiere = $course->getIdMatiere(); // Get Matiere associated with the course
                if ($matiere) {
                    $groupedCourses[$matiere->getNom()][] = $course; // Group courses by Matiere name
                }
            }
        }

        return $this->render('cours/student_courses.html.twig', [
            'classes' => $classes,
            'groupedCourses' => $groupedCourses, // Pass grouped courses
//            'courses' => $courses,
            'selectedClassId' => $selectedClassId
        ]);
    }


}


