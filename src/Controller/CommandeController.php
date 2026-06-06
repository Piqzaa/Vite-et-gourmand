<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\CommandeService;
use App\Service\MailService;
use DateTime;
use Exception;

class CommandeController {
    public function __construct(
        private MenuRepository $menuRepository,
        private UserRepository $userRepository,
        private AuthService $authService,
        private CommandeService $commandeService,
        private MailService $mailService
    ) {}

    /**
     * Affiche le formulaire de commande
     */
    public function index(): void {
        if (!$this->authService->isConnected()) {
            header('Location: connexion.php?redirect=commande.php' . (isset($_GET['menu']) ? '&menu=' . (int)$_GET['menu'] : ''));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userData = $this->userRepository->findById($userId);
        
        $user = [
            'prenom'          => $userData['prenom'] ?? '',
            'nom'             => $userData['nom'] ?? '',
            'email'           => $userData['email'] ?? '',
            'gsm'             => $userData['gsm'] ?? '',
            'adresse_postale' => $userData['adresse_postale'] ?? '',
            'ville'           => $userData['ville'] ?? ''
        ];

        $menuPreselect = isset($_GET['menu']) ? (int)$_GET['menu'] : 0;
        $menus = $this->menuRepository->findAllAvailable();

        $title = 'Commander un menu';
        $description = 'Remplissez le formulaire pour commander votre menu traiteur sur Vite & Gourmand.';

        require __DIR__ . '/../../views/commande.php';
    }

    /**
     * Traite la création d'une commande
     */
    public function create(): void {
        if (!$this->authService->isConnected()) {
            header('Location: connexion.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: commande.php');
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
                header('Location: commande.php?error=date_trop_proche');
                exit;
            }

            $userId = $_SESSION['user_id'];
            $commandeId = $this->commandeService->createCommande($data, $userId);

            // Envoi du mail de confirmation
            $this->sendConfirmationMail($commandeId);

            // Redirection selon le rôle
            $redirect = 'espace-utilisateur.php';
            if ($this->authService->isAdmin()) $redirect = 'espace-admin.php';
            elseif ($this->authService->isEmploye()) $redirect = 'espace-employe.php';

            header("Location: $redirect?success=1&commande=$commandeId");
            exit;

        } catch (Exception $e) {
            header('Location: commande.php?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function sendConfirmationMail(int $commandeId): void {
        $commande = $this->commandeService->getCommandeDetails($commandeId);
        if ($commande) {
            $htmlBody = "<h1>Confirmation de commande #$commandeId</h1>";
            $htmlBody .= "<p>Merci {$commande['prenom']} pour votre commande du menu <strong>{$commande['menu_titre']}</strong>.</p>";
            $htmlBody .= "<ul>
                <li>Date : {$commande['date_prestation']}</li>
                <li>Adresse : {$commande['adresse_livraison']}</li>
                <li>Total : {$commande['prix_total_ttc']}€</li>
            </ul>";

            $this->mailService->send(
                $commande['email'],
                $commande['prenom'] . ' ' . $commande['nom'],
                "Confirmation de votre commande #$commandeId - Vite & Gourmand",
                $htmlBody
            );
        }
    }
}
