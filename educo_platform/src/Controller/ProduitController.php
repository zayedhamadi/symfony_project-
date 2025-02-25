<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository,Request $request): Response
    {
        $search = $request->query->get('search');

        $queryBuilder = $produitRepository->createQueryBuilder('p');

        if ($search) {
            $queryBuilder->andWhere('p.nom LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $produits = $queryBuilder->getQuery()->getResult();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'search' => $search, // Passer la recherche pour garder la valeur dans l'input
        ]);
    }
//    #[Route('/boutique', name: 'app_boutique', methods: ['GET'])]
//    public function boutique(Request $request, ProduitRepository $produitRepository,PaginatorInterface $paginator, CategorieRepository $categorieRepository): Response
//    {
//        $search = $request->query->get('search');
//        $category = $request->query->get('category');
//
//        // Récupérer toutes les catégories
//        $categories = $categorieRepository->findAll();
//
//        $queryBuilder = $produitRepository->createQueryBuilder('p');
//
//        // Filtrer par catégorie
//        if ($category) {
//            $queryBuilder->andWhere('p.categorie = :category')
//                ->setParameter('category', $category);
//        }
//
//        // Recherche par nom
//        if ($search) {
//            $queryBuilder->andWhere('p.nom LIKE :search')
//                ->setParameter('search', '%' . $search . '%');
//        }
//
//        $produits = $queryBuilder->getQuery()->getResult();
//
//        $pagination = $paginator->paginate(
//            $queryBuilder->getQuery(),  // Requête
//            $request->query->getInt('page', 1), // Page actuelle
//            9 // Nombre d'éléments par page
//        );
//
//
//
//        // Passer les produits et catégories à la vue
//        return $this->render('produit/boutique.html.twig', [
//            'produits' => $produits,
//            'search' => $search,
//            'selectedCategory' => $category,
//            'categories' => $categories,  // Ajout des catégories
//        ]);
//    }

    #[Route('/boutique', name: 'app_boutique', methods: ['GET'])]
    public function boutique(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $categories = $categorieRepository->findAll();

        // Création de la requête pour récupérer les produits
        $queryBuilder = $produitRepository->createQueryBuilder('p');

        if ($category) {
            $queryBuilder->andWhere('p.categorie = :category')
                ->setParameter('category', $category);
        }

        if ($search) {
            $queryBuilder->andWhere('p.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Appliquer la pagination
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),  // Requête
            $request->query->getInt('page', 1), // Page actuelle
            9 // Nombre d'éléments par page
        );

        return $this->render('produit/boutique.html.twig', [
            'produits' => $pagination, // Doit être un objet `SlidingPaginationInterface`
            'search' => $search,
            'selectedCategory' => $category,
            'categories' => $categories,
        ]);
    }
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload du fichier.');
                }
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload du fichier.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/produit/details/{id}', name: 'app_produit_details_parent', methods: ['GET'])]
    public function detailsProduitParent(Produit $produit): Response
    {
        return $this->render('produit/detailsProduit_parent.html.twig', [
            'produit' => $produit,
        ]);
    }





}
