<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class PaiementController extends AbstractController
{
    #[Route('/commander-et-payer', name: 'app_commander_et_payer')]
    public function commanderEtPayer(Request $request, ProduitRepository $produitRepository, UserRepository $userRepository, EntityManagerInterface $entityManager,$stripeSK): Response
    {
        $session = $request->getSession();
        $userid = $session->get('user_id');
        $panier = $session->get('panier', []);
//echo ($userid);
//dd($session);
        if (empty($panier)) {
            return $this->render('paiement/error.html.twig', [
                'message' => 'Votre panier est vide.'
            ]);
        }
        $user = $userRepository->find($userid);

        if (!$user) {
            // Handle the case where the user doesn't exist
            throw $this->createNotFoundException('User not found.');
        }
        $commande = new Commande();
        $commande->setParent($user);
        $commande->setStatut('En attente');
        $commande->setDateCommande(new \DateTime());

        $total = 0;
        $lineItems = [];

        foreach ($panier as $produitId => $quantite) {
            $produit = $produitRepository->find($produitId);
            if ($produit) {
                // Créer une association CommandeProduit
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setCommande($commande);
                $commandeProduit->setProduit($produit);
                $commandeProduit->setQuantite($quantite);

                $entityManager->persist($commandeProduit);

                // Calculer le total
                $total += $produit->getPrix() * $quantite;

                // Ajouter à Stripe
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $produit->getNom(),
                        ],
                        'unit_amount' => $produit->getPrix() * 100,
                    ],
                    'quantity' => $quantite,
                ];
            }
        }
        $commande->setMontantTotal($total);
        $commande->setModePaiement('Carte'); // Ou 'Espèces' si paiement à l'administration

        // Persister la commande
        $entityManager->persist($commande);
        $entityManager->flush();

        // Initialisation Stripe
        Stripe::setApiKey($stripeSK);

        try {
            $stripeSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_paiement_success', ['id' => $commande->getId()], 0),
                'cancel_url' => $this->generateUrl('app_paiement_cancel', ['id' => $commande->getId()], 0),
            ]);

            // Supprimer le panier après validation
            $session->remove('panier');

            return $this->redirect($stripeSession->url, 303);
        } catch (\Exception $e) {
            return $this->render('paiement/error.html.twig', [
                'message' => 'Erreur lors du paiement : ' . $e->getMessage()
            ]);
        }
    }

    #[Route('/commande/{id}/paiement/success', name: 'app_paiement_success')]
    public function paiementSuccess(Commande $commande, EntityManagerInterface $entityManager)
    {
        $commande->setStatut('Payée');
        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', 'Votre paiement a été validé avec succès !');
        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/commande/{id}/paiement/cancel', name: 'app_paiement_cancel')]
    public function paiementCancel()
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('app_panier_view');
    }
}
