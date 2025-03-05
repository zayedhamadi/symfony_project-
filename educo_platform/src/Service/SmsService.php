<?php
namespace App\Service;

use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;

class SmsService
{
    private TexterInterface $texter;

    public function __construct(TexterInterface $texter)
    {
        $this->texter = $texter;
    }

    public function sendSms(string $phoneNumber, string $message): void
    {
        if (empty($phoneNumber)) {
            throw new \InvalidArgumentException('Le numéro de téléphone est vide.');
        }

        // Debug : afficher le numéro et le message avant d'envoyer
        dump("Envoi du SMS à : " . $phoneNumber);
        dump("Message : " . $message);

        // Création et envoi du SMS via Twilio
        $sms = new SmsMessage($phoneNumber, $message);
        $this->texter->send($sms);
    }
}
