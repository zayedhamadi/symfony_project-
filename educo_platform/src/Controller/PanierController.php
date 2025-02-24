<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_view')]
    public function viewCart(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get('panier', []);
        $panierAvecDetails = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $panierAvecDetails[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                ];
                $total += $produit->getPrix() * $quantite;
            }
        }

        return $this->render('panier/view.html.twig', [
            'items' => $panierAvecDetails,
            'total' => $total,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_add', methods: ['POST'])]
    public function addToCart(Request $request, Produit $produit, SessionInterface $session): Response
    {
        $quantite = $request->request->getInt('quantite', 1);
        $panier = $session->get('panier', []);

        if (isset($panier[$produit->getId()])) {
            $panier[$produit->getId()] += $quantite;
        } else {
            $panier[$produit->getId()] = $quantite;
        }

        $session->set('panier', $panier);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'message' => 'Produit ajouté au panier !',
                'panier_total' => array_sum($panier),
            ]);
        }

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/supprimer/{id}', name: 'app_panier_remove', methods: ['POST'])]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', 'Produit retiré du panier.');
        }

        return $this->redirectToRoute('app_panier_view');
    }
//    #[Route('/panier/update/{id}', name: 'app_panier_update_quantite', methods: ['POST'])]
//    public function updateQuantity($id, Request $request, SessionInterface $session)
//    {
//        // Récupérer la quantité soumise par l'utilisateur
//        $quantite = (int) $request->request->get('quantite');
//
//        // Vérifier si la quantité est valide
//        if ($quantite < 1) {
//            // Rediriger en cas de quantité invalide
//            $this->addFlash('error', 'Quantité invalide.');
//            return $this->redirectToRoute('app_panier_view');
//        }
//
//        // Récupérer le panier depuis la session
//        $panier = $session->get('panier', []);
//
//        // Vérifier si le produit existe dans le panier
//        if (isset($panier[$id])) {
//            // Mettre à jour la quantité du produit
//            $panier[$id] = $quantite;
//        }
//
//        // Sauvegarder le panier dans la session
//        $session->set('panier', $panier);
//
//        // Rediriger vers la page du panier
//        $this->addFlash('success', 'Quantité mise à jour !');
//        return $this->redirectToRoute('app_panier_view');
//    }
    #[Route('/update/{id}', name: 'app_panier_update_quantite', methods: ['POST'])]
    public function updateQuantity($id, Request $request, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $quantite = (int) $request->request->get('quantite');

        if ($quantite < 1) {
            return $this->json(['success' => false, 'message' => 'Quantité invalide.'], 400);
        }

        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            return $this->json(['success' => false, 'message' => 'Produit introuvable dans le panier.'], 404);
        }

        $produit = $produitRepository->find($id);
        if (!$produit) {
            return $this->json(['success' => false, 'message' => 'Produit non trouvé.'], 404);
        }

        $panier[$id] = $quantite;
        $session->set('panier', $panier);

        // Recalculer le total du panier
        $totalPanier = 0;
        foreach ($panier as $prodId => $qte) {
            $p = $produitRepository->find($prodId);
            if ($p) {
                $totalPanier += $p->getPrix() * $qte;
            }
        }

        return $this->json([
            'success' => true,
            'total_produit' => $produit->getPrix() * $quantite,
            'total_panier' => $totalPanier
        ]);
    }

    #[Route('/vider', name: 'app_panier_clear', methods: ['POST'])]
    public function clearCart(SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('app_panier_view');
    }
}
