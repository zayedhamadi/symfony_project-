<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig');
    }

    #[Route('/commande/paiement', name: 'app_commande_paiement')]
    public function choixPaiement(): Response
    {
        return $this->render('commande/paiement.html.twig');
    }

    #[Route('/commande/paiement/carte', name: 'app_paiement_carte')]
    public function paiementCarte(): Response
    {
        return $this->render('commande/paiement_carte.html.twig');
    }

    #[Route('/commande/paiement/livraison', name: 'app_paiement_livraison')]
    public function paiementLivraison(): Response
    {
        return $this->render('commande/paiement_livraison.html.twig');
    }
    #[Route('/commande/valider/livraison', name: 'app_commande_valider_livraison')]
    public function validerCommandeLivraison(Request $request, SessionInterface $session): Response
    {
        $user = $this->getUser();

        // Récupérer le panier de la session
        $panier = $session->get('panier', []);

        // Si le panier est vide, rediriger
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_view');
        }

        // Calculer le montant total de la commande
        $montantTotal = 0;
        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $montantTotal += $produit->getPrix() * $quantite;
            }
        }

        // Créer une nouvelle commande avec paiement à la livraison
        $commande = new Commande();
        $commande->setParent($user);  // L'utilisateur qui a passé la commande
        $commande->setDateCommande(new \DateTime());
        $commande->setMontantTotal($montantTotal);
        $commande->setStatut('En attente de paiement à l\'administration');
        $commande->setModePaiement('Livraison');  // Mode de paiement : livraison

        // Ajouter les produits du panier à la commande
        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                // Créer un nouvel objet CommandeProduit pour associer le produit à la commande
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setProduit($produit);
                $commandeProduit->setQuantite($quantite); // Définir la quantité
                $commandeProduit->setCommande($commande); // Associer à la commande

                // Persister la nouvelle entité CommandeProduit
                $this->entityManager->persist($commandeProduit);

                // Décrémenter la quantité du produit en stock
                $produit->setQuantite($produit->getQuantite() - $quantite);
                $this->entityManager->persist($produit);
            }
        }

        // Sauvegarder la commande
        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        // Vider le panier après la commande
        $session->remove('panier');

        // Ajouter un message de succès
        $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');

        // Rediriger vers la page de confirmation de commande
        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/commande/valider', name: 'app_commande_valider')]
    public function validerCommande(Request $request, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer le panier de la session
        $panier = $session->get('panier', []);

        // Si le panier est vide, rediriger
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_view');
        }

        // Calculer le montant total de la commande
        $montantTotal = 0;
        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $montantTotal += $produit->getPrix() * $quantite;
            }
        }

        // Créer une nouvelle commande
        $commande = new Commande();
        $commande->setParent($user);  // L'utilisateur qui a passé la commande
        $commande->setDateCommande(new \DateTime());
        $commande->setMontantTotal($montantTotal);
        $commande->setStatut('En attente');
        $commande->setModePaiement(''); // Mode de paiement à définir plus tard

        // Ajouter les produits du panier à la commande
        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                // Créer un nouvel objet CommandeProduit pour associer le produit à la commande
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setProduit($produit);
                $commandeProduit->setQuantite($quantite); // Définir la quantité
                $commandeProduit->setCommande($commande); // Associer à la commande

                // Persister la nouvelle entité CommandeProduit
                $this->entityManager->persist($commandeProduit);

                // Décrémenter la quantité du produit en stock, si c'est nécessaire pour la gestion du stock
                // Si vous avez une propriété de stock dans l'entité Produit, vous pouvez la gérer ici.
                $produit->setStock($produit->getStock() - $quantite); // Supposons que vous avez une propriété stock
                $this->entityManager->persist($produit); // Persister le produit mis à jour
            }
        }



        // Sauvegarder la commande
        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        // Vider le panier après la commande
        $session->remove('panier');

        // Ajouter un message de succès
        $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');

        // Rediriger vers la page de confirmation de commande
        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/commande/confirmation/{id}', name: 'app_commande_confirmation')]
    public function confirmationCommande(int $id): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/paiement/carte/traiter', name: 'app_paiement_carte_traiter')]
    public function traiterPaiementCarte(int $id): Response
    {
        // Récupérer la commande par ID
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Changer le statut de la commande après paiement
        $commande->setStatut('Payée');

        // Sauvegarder la commande mise à jour
        $this->entityManager->flush();

        // Ajouter un message de confirmation
        $this->addFlash('success', 'Votre paiement a été effectué avec succès !');

        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/commande/paiement/livraison/traiter', name: 'app_paiement_livraison_traiter')]
    public function traiterPaiementLivraison(int $id): Response
    {
        // Récupérer la commande par ID
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Changer le statut de la commande après paiement à livraison
        $commande->setStatut('En attente de paiement à l\'administration');

        // Sauvegarder la commande mise à jour
        $this->entityManager->flush();

        // Ajouter un message de confirmation
        $this->addFlash('success', 'Votre commande a été enregistrée pour un paiement à l\'administration !');

        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/commande/admin', name: 'app_commande_admin')]
    public function consulterCommandesAdmin(): Response
    {
        // Récupérer toutes les commandes
        $commandes = $this->entityManager->getRepository(Commande::class)->findAll();

        // Afficher les commandes dans une vue
        return $this->render('commande/admin.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/{id}', name: 'app_commande_details')]
    public function detailsCommande(int $id): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('commande/details.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/{id}/marquer-prete', name: 'app_commande_marquer_prete')]
    public function marquerCommandePrete(int $id): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Mettre à jour le statut de la commande
        $commande->setStatut('Prêt');
        $this->entityManager->flush();

        // Ajouter un message de confirmation
        $this->addFlash('success', 'La commande a été marquée comme prêt.');

        return $this->redirectToRoute('app_commande_details', ['id' => $id]);
    }


}
