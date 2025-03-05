<?php



namespace App\Controller;
use App\Entity\Eleve;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;


final class DashboardParent extends AbstractController
{
    private $entityManager;

    // Injecter l'EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Page d'accueil du dashboard parent
    #[Route('/dashboard/parent', name: 'app_dashboard_parent')]
    public function index(): Response
    {
        $user = $this->getUser();

    // Vérifier si l'utilisateur est connecté
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Vérifier si l'utilisateur a le rôle 'ROLE_PARENT'
    if (!in_array('ROLE_PARENT', $user->getRoles(), true)) {
        return $this->redirectToRoute('app_login');
    }

    // Récupérer les élèves liés au parent connecté
    $eleves = $this->entityManager->getRepository(Eleve::class)->findBy(['parent' => $user]);

    return $this->render('dashborad_parent/profile.html.twig', [
        'user' => $user,
        'eleves' => $eleves, // Passer les élèves au template
    ]);
    }

    // Liste des élèves
    #[Route('/dashboard/parent/liste-eleves', name: 'app_dashboard_parent_liste_eleves')]
    public function listeEleve(): Response
    {
        // Récupérer tous les élèves
        $eleves = $this->entityManager->getRepository(Eleve::class)->findAll();

        return $this->render('dashborad_parent/listeofstudent.html.twig', [
            'eleves' => $eleves,
        ]);
    }

    // Détails d'un élève spécifique
    #[Route('/dashboard/parent/eleve/{id}', name: 'app_dashboard_parent_eleve_details')]
    public function detailsEleve(int $id): Response
    {
        // Récupérer un élève par son ID
        $eleve = $this->entityManager->getRepository(Eleve::class)->find($id);

        if (!$eleve) {
            throw $this->createNotFoundException('Élève non trouvé');
        }

        return $this->render('dashborad_parent/detailsEleve.html.twig', [
            'eleve' => $eleve,
        ]);
    }
}
