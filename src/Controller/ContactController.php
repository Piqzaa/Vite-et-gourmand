<?php

namespace App\Controller;

class ContactController {
    public function index() {
      $title = "Contactez-nous - Vite et Gourmand";
      $description = "Contactez Vite & Gourmand pour toute question ou demande particulière concernant nos menus traiteur à Bordeaux.";

      include __DIR__ . '/../../views/contact.php';
    }

    public function submit() {
        // Ici vous traiteriez le formulaire de contact, par exemple en envoyant un email
        // ou en enregistrant la demande dans une base de données.

        // Pour l'instant, on redirige simplement vers la page de contact avec un message de succès
        header('Location: index.php?page=contact&success=1');
        exit;
    }
}