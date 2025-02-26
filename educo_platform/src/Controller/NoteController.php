<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[Route('/note')]
class NoteController extends AbstractController
{
    #[Route('/list', name: 'note_list', methods: ['GET'])]
    public function list(NoteRepository $noteRepository): Response
    {
        $notes = $noteRepository->findAll();

        return $this->render('note/list.html.twig', [
            'notes' => $notes,
        ]);
    }
}
