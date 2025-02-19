<?php

namespace App\Controller;

use App\Entity\TestEntity;
use App\Form\TestEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test/entity')]
final class TestEntityController extends AbstractController
{
    #[Route(name: 'app_test_entity_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $testEntities = $entityManager
            ->getRepository(TestEntity::class)
            ->findAll();

        return $this->render('test_entity/index.html.twig', [
            'test_entities' => $testEntities,
        ]);
    }

    #[Route('/new', name: 'app_test_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $testEntity = new TestEntity();
        $form = $this->createForm(TestEntityType::class, $testEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($testEntity);
            $entityManager->flush();

            return $this->redirectToRoute('app_test_entity_index', []);
        }

        return $this->render('test_entity/new.html.twig', [
            'test_entity' => $testEntity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test_entity_show', methods: ['GET'])]
    public function show(TestEntity $testEntity): Response
    {
        return $this->render('test_entity/show.html.twig', [
            'test_entity' => $testEntity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_test_entity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TestEntity $testEntity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TestEntityType::class, $testEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_test_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test_entity/edit.html.twig', [
            'test_entity' => $testEntity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test_entity_delete', methods: ['POST'])]
    public function delete(Request $request, TestEntity $testEntity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$testEntity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($testEntity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_test_entity_index', [], Response::HTTP_SEE_OTHER);
    }
}
