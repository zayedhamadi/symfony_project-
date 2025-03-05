<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class TestSmsController extends AbstractController
{
    #[Route('/test-sms', name: 'send_sms')]
    public function sendSms(): Response
    {
        // Remplacez ces valeurs par vos informations Twilio
        $sid = 'ACcd3ce9565fdb4475a530755d657a0f8e'; // Votre SID Twilio
        $token = '710d10826fa8cc9b64878d9504b2a7de'; // Votre token Twilio
        $fromNumber = '+19035641839'; // Votre numéro Twilio
        $toNumber = '+21624942273'; // Le numéro de destination

        // Initialise le client Twilio
        $twilio = new Client($sid, $token);

        try {
            // Envoie le SMS
            $message = $twilio->messages
                ->create($toNumber, // to
                    [
                        "body" => "Test SMS depuis Symfony avec Twilio directement!",
                        "from" => $fromNumber
                    ]
                );

            // Affiche l'identifiant du message (SID)
            return new Response('SMS envoyé avec succès ! SID : ' . $message->sid);
        } catch (\Exception $e) {
            // En cas d'erreur, affiche le message d'erreur
            return new Response('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }
    }
}