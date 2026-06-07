<?php
namespace App\Controller;

class LegalController {
    public function cgv(): void {
        $title = 'Conditions Générales de Vente';
        $description = 'Découvrez les conditions générales de vente de Vite & Gourmand.';
        
        require __DIR__ . '/../../views/cgv.php';
    }

    public function mentions(): void {
        $title = 'Mentions Légales';
        $description = 'Consultez les mentions légales de Vite & Gourmand.';
        
        require __DIR__ . '/../../views/mentions-legales.php';
    }
}