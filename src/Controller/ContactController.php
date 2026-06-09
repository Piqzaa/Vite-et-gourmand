<?php

namespace App\Controller;

use App\Service\MailService;
use App\Service\LoggerService;
use App\Service\SecurityService;

class ContactController {
    public function __construct(
        private ?MailService $mailService = null,
        private ?LoggerService $logger = null,
        private ?SecurityService $securityService = null,
    ) {}

    public function index() {
      if ($this->securityService) {
          $this->securityService->generateCsrfToken();
      }
      $title = "Contactez-nous - Vite et Gourmand";
      $description = "Contactez Vite & Gourmand pour toute question ou demande particulière concernant nos menus traiteur à Bordeaux.";

      require __DIR__ . '/../../views/contact.php';
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=contact');
            exit;
        }

        if ($this->securityService && !$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=contact&error=csrf_invalid');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($email) || empty($titre) || empty($message)) {
            header('Location: index.php?page=contact&error=champs_vides');
            exit;
        }

        // 1. Logging
        if ($this->logger) {
            $this->logger->log('contact_form_submit', ['email' => $email, 'subject' => $titre]);
        }

        // 2. Envoi de l'email (vers l'admin)
        $emailSent = false;
        if ($this->mailService) {
            $htmlBody = "<h2>Nouveau message de contact</h2>";
            $htmlBody .= "<p><strong>De :</strong> $email</p>";
            $htmlBody .= "<p><strong>Sujet :</strong> $titre</p>";
            $htmlBody .= "<p><strong>Message :</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

            $emailSent = $this->mailService->send(
                getenv('SMTP_FROM') ?: 'admin@viteetgourmand.fr',
                'Admin Vite & Gourmand',
                "Nouveau message : $titre",
                $htmlBody
            );
        }

        if ($emailSent) {
            header('Location: index.php?page=contact&success=1');
        } else {
            // Si le mail échoue, on log l'erreur mais on peut quand même dire que c'est ok si on a une BDD
            // Pour l'instant on redirige avec une erreur si le mail ne part pas
            header('Location: index.php?page=contact&error=mail_failed');
        }
        exit;
    }
}
