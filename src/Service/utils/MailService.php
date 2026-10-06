<?php

namespace App\Service\utils;
use App\Dto\messages\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class MailService
{
    public function __construct(
        private MessageBusInterface $bus // Remplacement du Mailer par le Bus Messenger
    ) {}

    public function sendEmail(string $to, string $subject, string $body): void
    {
               // Au lieu d'envoyer le mail, on met l'action en file d'attente RabbitMQ
        $this->bus->dispatch(new SendEmailMessage($to, $subject, $body));

    }
    public function getHtmlMail(string $nom, string $message): string
    {
        return "
            <html>
                <body style='font-family: Arial, sans-serif'>
                    <h2>Bonjour $nom</h2>
                    <p>$message</p>
                    <br>
                    <p>Cordialement,<br>Mesupres</p>
                </body>
            </html>
        ";
    }

    public function sendRapportReminder(string $destinataire, string $typeRapport, string $dateLimite): void
    {
        $subject = "Rappel - Envoi de votre rapport {$typeRapport} sur la plateforme Tatitra Mesupres";
        
        $htmlContent = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <p>Madame, Monsieur,</p>
                <p>Rappel : La date limite d'envoi de votre rapport <strong>{$typeRapport}</strong> est fixée au <strong>{$dateLimite}</strong> à <strong>12 heures</strong>.</p>
                <p>Lien : <a href='https://rapport.mesupres.mg'>https://rapport.mesupres.mg</a></p>
                <p>À ce jour, sauf erreur de notre part, celui-ci n'a pas encore été reçu. Merci de bien vouloir le transmettre dans les meilleurs délais.</p>
                <p>Besoin d'aide : <strong>034 05 516 08</strong>, chef de service Système d'information</p>
                <p>Cordialement,<br><strong>Mesupres</strong></p>
                <br>
                <p style='font-size: 12px; color: #888;'>N.B : Ne pas répondre à ce mail.</p>
            </div>
        ";

        $this->sendEmail($destinataire, $subject, $htmlContent);
    }
    
}
