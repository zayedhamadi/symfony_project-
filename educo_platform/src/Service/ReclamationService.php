<?php
namespace App\Service;

use App\Entity\Reclamation;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

class ReclamationService
{
    private EntityManagerInterface $em;
    private MailService $mailService;

    public function __construct(EntityManagerInterface $em, MailService $mailService)
    {
        $this->em = $em;
        $this->mailService = $mailService;
    }

    public function traiterReclamation(Reclamation $reclamation): void
    {
        $reclamation->setStatut('Traitée');
        $this->em->flush();

        // Envoi de l'email si le statut est "Traitée"
        if ($reclamation->getStatut() === 'Traitée') {
            $this->mailService->envoyerEmail(
                $reclamation->getUser()->getEmail(),
                "Votre réclamation a été traitée",
                "Bonjour, votre réclamation (#{$reclamation->getId()}) a été traitée. Merci de vérifier votre espace utilisateur."
            );
        }
    }
}
