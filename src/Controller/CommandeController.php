<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\CommandeService;
use App\Service\MailService;
use App\Service\SecurityService;
use DateTime;
use Exception;

class CommandeController {
    public function __construct(
        private MenuRepository $menuRepository,
        private UserRepository $userRepository,
        private AuthService $authService,
        private CommandeService $commandeService,
        private MailService $mailService,
        private SecurityService $securityService
    ) {}

    /**
     * Affiche le formulaire de commande
     */
    public function index(): void {
        $this->securityService->generateCsrfToken();

        if (!$this->authService->isConnected()) {
            $redirectUrl = 'index.php?page=commande';
            if (isset($_GET['menu'])) {
                $redirectUrl .= '&menu=' . (int)$_GET['menu'];
            }
            header('Location: index.php?page=login&redirect=' . urlencode($redirectUrl));
            exit;
        }

        $this->securityService->generateCsrfToken();
        $userId = $_SESSION['user_id'];
        $userData = $this->userRepository->findById($userId);
        
        $user = [
            'prenom'          => $userData->getPrenom(),
            'nom'             => $userData->getNom(),
            'email'           => $userData->getEmail(),
            'gsm'             => $userData->getGsm(),
            'adresse_postale' => $userData->getAdressePostale(),
            'ville'           => $userData->getVille()
        ];

        $menuPreselect = isset($_GET['menu']) ? (int)$_GET['menu'] : 0;
        $menus = $this->menuRepository->findAllAvailable();

        $title = 'Commander un menu';
        $description = 'Remplissez le formulaire pour commander votre menu traiteur sur Vite & Gourmand.';

        require __DIR__ . '/../../views/commande.php';
    }

    public function create(): void {
        if (!$this->authService->isConnected()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=commande&error=csrf_invalid');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=commande');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=commande&error=csrf_invalid');
            exit;
        }

        try {
            $data = [
                'menu_id'           => (int)($_POST['menu_id'] ?? 0),
                'nb_personnes'      => (int)($_POST['nb_personnes'] ?? 0),
                'adresse_livraison' => trim($_POST['adresse_livraison'] ?? ''),
                'ville'             => trim($_POST['ville_livraison'] ?? ''),
                'date_livraison'    => $_POST['date_livraison'] ?? '',
                'heure_livraison'   => $_POST['heure_livraison'] ?? ''
            ];

            // Validation date (min 72h)
            $dateObj = new DateTime($data['date_livraison']);
            $now = new DateTime();
            $diff = $now->diff($dateObj);
            $hours = ($diff->days * 24) + $diff->h;

            if ($dateObj <= $now || $hours < 72) {
                header('Location: index.php?page=commande&error=date_trop_proche');
                exit;
            }

            $userId = $_SESSION['user_id'];
            $commandeId = $this->commandeService->createCommande($data, $userId);

            // Envoi du mail de confirmation
            $this->sendConfirmationMail($commandeId);

            // Redirection selon le rôle
            $redirect = 'index.php?page=espace-utilisateur';
            if ($this->authService->isAdmin()) $redirect = 'index.php?page=espace-admin';
            elseif ($this->authService->isEmploye()) $redirect = 'index.php?page=espace-employe';

            header("Location: $redirect&success=1&commande=$commandeId");
            exit;

        } catch (Exception $e) {
            header('Location: index.php?page=commande&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function sendConfirmationMail(int $commandeId): void {
        $commande = $this->commandeService->getCommandeDetails($commandeId);
        if ($commande) {
            $htmlBody = "<h1>Confirmation de commande #$commandeId</h1>";
            $htmlBody .= "<p>Merci {$commande->getClientPrenom()} pour votre commande du menu <strong>{$commande->getMenuNom()}</strong>.</p>";
            $htmlBody .= "<ul>
                <li>Date : {$commande->getDatePrestation()->format('Y-m-d')}</li>
                <li>Adresse : {$commande->getAdresseLivraison()}</li>
                <li>Total : {$commande->getPrixTotalTtc()}€</li>
            </ul>";

            $this->mailService->send(
                $commande->getClientEmail(),
                $commande->getClientPrenom() . ' ' . $commande->getClientNom(),
                "Confirmation de votre commande #$commandeId - Vite & Gourmand",
                $htmlBody
            );
        }
    }
}
