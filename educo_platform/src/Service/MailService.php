<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function envoyerEmail(string $destinataire, string $sujet, string $contenu): void
    {
        $email = (new Email())
            ->from('educolearning@educo.com')
            ->to($destinataire)
            ->subject($sujet)
            ->text($contenu);

        $this->mailer->send($email);
    }
}
