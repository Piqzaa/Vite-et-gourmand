<?php

namespace App\Controller;

use App\Service\MailService;
use App\Service\LoggerService;
use App\Service\SecurityService;
use App\Service\RateLimiter;

class ContactController {
    public function __construct(
        private SecurityService $securityService,
        private RateLimiter $rateLimiter,
        private ?MailService $mailService = null,
        private ?LoggerService $logger = null,
        private ?SecurityService $securityService = null,
    ) {}

    public function index() {
      $this->securityService->generateCsrfToken();
      $title = "Contactez-nous - Vite et Gourmand";
      $description = "Contactez Vite & Gourmand pour toute question ou demande particulière concernant nos menus traiteur à Bordeaux.";

      require __DIR__ . '/../../views/contact.php';
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=contact');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['contact_error'] = 'Session invalide. Veuillez réessayer.';
            header('Location: index.php?page=contact');
            exit;
        }

        if (!$this->rateLimiter->isAllowed('contact')) {
            $_SESSION['contact_error'] = 'Trop de tentatives. Réessayez plus tard.';
            header('Location: index.php?page=contact');
            exit;
        }

        $this->rateLimiter->increment('contact');

        $email = trim($_POST['email'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($email) || empty($titre) || empty($message)) {
            $_SESSION['contact_error'] = 'Veuillez remplir tous les champs.';
            header('Location: index.php?page=contact');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['contact_error'] = 'Adresse email invalide.';
            header('Location: index.php?page=contact');
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
            $htmlBody .= "<p><strong>De :</strong> " . htmlspecialchars($email) . "</p>";
            $htmlBody .= "<p><strong>Sujet :</strong> " . htmlspecialchars($titre) . "</p>";
            $htmlBody .= "<p><strong>Message :</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

            $emailSent = $this->mailService->send(
                getenv('SMTP_FROM') ?: 'admin@viteetgourmand.fr',
                'Admin Vite & Gourmand',
                "Nouveau message : " . htmlspecialchars($titre),
                $htmlBody
            );
        }

        if ($emailSent) {
            $_SESSION['contact_success'] = 'Message envoyé avec succès. Nous vous répondrons rapidement.';
        } else {
            $_SESSION['contact_error'] = "Erreur lors de l'envoi du message. Veuillez réessayer.";
        }
        header('Location: index.php?page=contact');
        exit;
    }
}
