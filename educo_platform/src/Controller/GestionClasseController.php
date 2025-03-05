<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/gestion/classe')]
final class GestionClasseController extends AbstractController
{
    #[Route(name: 'app_gestion_classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository,Request $request): Response
    {
        $searchTerm = $request->query->get('search');  

        // Recherche des classes selon le terme  
        if ($searchTerm) {  
            $classes = $classeRepository->searchByName($searchTerm);  
        } else {  
            $classes = $classeRepository->findAll();  
        }  

        // Compter les classes par niveau  
        $niveauCounts = $classeRepository->countClassesByNiveau($searchTerm);  

        // Regrouper les classes par le premier caractère (niveau)  
        $classesGroupedByChar = [];  
        foreach ($classes as $classe) {  
            $firstChar = substr($classe->getNomClasse(), 0, 1); // Prendre le premier caractère  
            if (!isset($classesGroupedByChar[$firstChar])) {  
                $classesGroupedByChar[$firstChar] = [];  
            }  
            $classesGroupedByChar[$firstChar][] = $classe; // Regrouper les classes par premier caractère  
        }  

        return $this->render('gestion_classe/index.html.twig', [  
            'classesGroupedByChar' => $classesGroupedByChar,  
            'searchTerm' => $searchTerm,  
            'niveauCounts' => $niveauCounts, // Pas de transformation ici, utilisation directe  
        ]);  
    }

    

    
    #[Route('/new', name: 'app_gestion_classe_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    // Crée une nouvelle instance de Classe
    $classe = new Classe();
    $form = $this->createForm(ClasseType::class, $classe);
    $form->handleRequest($request);

    // Vérifie si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Persiste la nouvelle classe en base de données
        $entityManager->persist($classe);
        $entityManager->flush();

        try {
            foreach ($classe->getIdUser() as $enseignant) {
                $email = (new Email())
                    ->from('admin@votre-ecole.com')
                    ->to($enseignant->getEmail()) 
                    ->subject('Nouvelle Classe Assignée') 
                    ->text("Bonjour " . $enseignant->getNom() . ",\n\nUne nouvelle classe vous a été attribuée : " . $classe->getNomClasse());

                $mailer->send($email);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }

        // Redirige vers la liste des classes après la création
        return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
    }

    // Affiche le formulaire de création d'une nouvelle classe
    return $this->render('gestion_classe/new.html.twig', [
        'classe' => $classe,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_gestion_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        // Affiche les détails d'une classe spécifique
        return $this->render('gestion_classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_gestion_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire pour modifier une classe existante
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour la classe en base de données
            $entityManager->flush();

            // Redirige vers la liste des classes après la modification
            return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire de modification de la classe
        return $this->render('gestion_classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->getPayload()->getString('_token'))) {
            // Supprime la classe de la base de données
            $entityManager->remove($classe);
            $entityManager->flush();
        }

        // Redirige vers la liste des classes après la suppression
        return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_gestion_classe_pdf', methods: ['GET'])]
    public function generatePdf(Classe $classe, Pdf $knpSnappyPdf): Response
    {
        // Rendu du template Twig en HTML
        $html = $this->renderView('gestion_classe/pdf.html.twig', [
            'classe' => $classe,
        ]);

        // Générer le PDF
        $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

        // Retourner le PDF en réponse
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="liste_eleves_classe_' . $classe->getId() . '.pdf"',
            ]
        );
    }



    #[Route('/{id}/excel', name: 'app_gestion_classe_excel', methods: ['GET'])]
    public function exportToExcel(Classe $classe): Response
    {
        // Crée une nouvelle instance de Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définit les en-têtes de colonnes
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom de la Classe');
        $sheet->setCellValue('C1', 'Enseignants');
        $sheet->setCellValue('D1', 'Élèves');
        $sheet->setCellValue('E1', 'Parent');
        $sheet->setCellValue('F1', 'Email du Parent');
        $sheet->setCellValue('G1', 'Téléphone du Parent');
        $sheet->setCellValue('H1', 'Moyenne');
        $sheet->setCellValue('I1', 'Nombre d\'Absences');

        // Remplit les données de la classe
        $row = 2;
        $sheet->setCellValue('A' . $row, $classe->getId());
        $sheet->setCellValue('B' . $row, $classe->getNomClasse());

        // Liste des enseignants
        $enseignants = [];
        foreach ($classe->getIdUser() as $enseignant) {
            $enseignants[] = $enseignant->getNom() . ' ' . $enseignant->getPrenom();
        }
        $sheet->setCellValue('C' . $row, implode(', ', $enseignants));

        // Liste des élèves
        foreach ($classe->getEleves() as $eleve) {
            $sheet->setCellValue('D' . $row, $eleve->getNom() . ' ' . $eleve->getPrenom());
            $sheet->setCellValue('E' . $row, $eleve->getIdParent() ? $eleve->getIdParent()->getNom() . ' ' . $eleve->getIdParent()->getPrenom() : 'Aucun parent');
            $sheet->setCellValue('F' . $row, $eleve->getIdParent() ? $eleve->getIdParent()->getEmail() : 'Non disponible');
            $sheet->setCellValue('G' . $row, $eleve->getIdParent() ? $eleve->getIdParent()->getNumTel() : 'Non disponible');
            $sheet->setCellValue('H' . $row, $eleve->getMoyenne());
            $sheet->setCellValue('I' . $row, $eleve->getNbreAbscence());
            $row++;
        }

        // Crée un writer pour exporter en format Excel
        $writer = new Xlsx($spreadsheet);

        // Crée un fichier temporaire
        $fileName = 'classe_' . $classe->getId() . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        // Sauvegarde le fichier Excel
        $writer->save($tempFile);

        // Retourne le fichier Excel en réponse
        return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
}