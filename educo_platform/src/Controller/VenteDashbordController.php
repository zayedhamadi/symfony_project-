<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VenteDashbordController extends AbstractController
{


    #[Route('/vente/dashbord', name: 'admin_dashboard')]
    public function index(CommandeRepository $commandeRepo, ProduitRepository $produitRepo): Response
    {
        $totalVentes = $commandeRepo->getTotalVentes();
        $commandesEnAttente = $commandeRepo->countCommandesEnAttente();
        $produitsPopulaires = $produitRepo->getProduitsLesPlusVendus();
        $totalCommandes = $commandeRepo->countTotalCommandes();  // Ajouter la méthode dans le repository
        $chiffreAffaires = $commandeRepo->getChiffreAffaires();  // Ajouter la méthode dans le repository
        $moyenneCommandes = $commandeRepo->getMoyenneCommande(); // Ajouter la méthode dans le repository
        $commandesRecentes = $commandeRepo->getCommandesRecentes(); // Ajouter la méthode dans le repository

        return $this->render('vente_dashbord/dashboard.html.twig', [
            'totalVentes' => $totalVentes,
            'commandesEnAttente' => $commandesEnAttente,
            'produitsPopulaires' => $produitsPopulaires,
            'totalCommandes' => $totalCommandes,
            'chiffreAffaires' => $chiffreAffaires,
            'moyenneCommandes' => $moyenneCommandes,
            'commandesRecentes' => $commandesRecentes,
        ]);
    }

}
