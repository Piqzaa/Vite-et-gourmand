<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use App\Repository\PlatRepository;
use App\Repository\HoraireRepository;
use App\Repository\AllergeneRepository;
use App\Repository\ThemeRepository;
use App\Repository\RegimeRepository;
use App\Service\AuthService;
use App\Service\FileService;
use Exception;

class AdminController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private UserRepository $userRepo,
        private MenuRepository $menuRepo,
        private AvisRepository $avisRepo,
        private PlatRepository $platRepo,
        private HoraireRepository $horaireRepo,
        private AllergeneRepository $allergeneRepo,
        private ThemeRepository $themeRepo,
        private RegimeRepository $regimeRepo,
        private AuthService $authService,
        private FileService $fileService
    ) {}

    public function index(): void {
        if (!$this->authService->isAdmin()) {
            if ($this->authService->isEmploye()) {
                header('Location: index.php?page=espace-employe');
                exit;
            }
            header('Location: index.php?page=login');
            exit;
        }

        $commandesRaw = $this->commandeRepo->findAllWithDetails();
        $commandes = [];
        foreach ($commandesRaw as $cmd) {
            $commandes[] = [
                'commande_id' => $cmd->getId(),
                'menu_nom' => $cmd->getMenuNom(),
                'client_nom' => $cmd->getClientNom(),
                'client_prenom' => $cmd->getClientPrenom(),
                'client_email' => $cmd->getClientEmail(),
                'client_gsm' => $cmd->getClientGsm(),
                'statut' => $cmd->getStatut(),
                'date_prestation' => $cmd->getDatePrestation()->format('Y-m-d'),
                'heure_prestation' => $cmd->getHeurePrestation(),
                'adresse_livraison' => $cmd->getAdresseLivraison(),
                'prix_total_ttc' => $cmd->getPrixTotalTtc()
            ];
        }
        $employes = $this->userRepo->findByRole('employe');
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $statsCA = $this->commandeRepo->getGlobalStats();
        $statsParMenu = $this->commandeRepo->getStatsByMenu();
        $horaires = $this->horaireRepo->findAll();
        $securityService = new \App\Service\SecurityService();

        $title = 'Espace de gestion';
        $description = 'Gérez les commandes, les employés, les menus et les avis clients.';
        require __DIR__ . '/../../views/espace-admin.php';
    }

    /**
     * Affiche le formulaire de création de plat
     */
    public function createPlat(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $allergenes = $this->allergeneRepo->findAll();
        $title = 'Créer un plat';
        
        require __DIR__ . '/../../views/plat-create.php';
    }

    /**
     * Traite la création d'un plat
     */
    public function processCreatePlat(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=plat-create');
            exit;
        }

        $libelle = trim($_POST['libelle'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $allergenes = $_POST['allergenes'] ?? [];

        if (!$libelle || !in_array($type, ['entrée', 'plat', 'dessert'])) {
            header('Location: index.php?page=plat-create&error=champs_manquants');
            exit;
        }

        // Gestion de l'upload d'image via le FileService
        $imagePath = null;
        try {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                header('Location: index.php?page=plat-create&error=image_manquante');
                exit;
            }

            $imagePath = $this->fileService->uploadImage($_FILES['image'], 'plat_');
        } catch (Exception $e) {
            // Mapping des messages d'erreur du service vers des codes d'erreur URL
            $errorMap = [
                "Format d'image non supporté" => 'format_invalide',
                "Le fichier est trop volumineux" => 'fichier_trop_lourd',
                "Erreur lors du transfert" => 'upload_echoue'
            ];
            
            $errorCode = 'upload_echoue';
            foreach ($errorMap as $msg => $code) {
                if (str_contains($e->getMessage(), $msg)) {
                    $errorCode = $code;
                    break;
                }
            }
            header("Location: index.php?page=plat-create&error=$errorCode");
            exit;
        }

        try {
            $this->platRepo->beginTransaction();
            
            $platId = $this->platRepo->save([
                'libelle' => $libelle,
                'type' => $type,
                'image_path' => $imagePath
            ]);

            if (!empty($allergenes)) {
                $this->platRepo->linkAllergenes($platId, $allergenes);
            }

            $this->platRepo->commit();
            
            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=plat_cree#menus");
            exit;

        } catch (Exception $e) {
            $this->platRepo->rollBack();
            if ($imagePath) $this->fileService->deleteFile($imagePath);
            header('Location: index.php?page=plat-create&error=erreur_serveur');
            exit;
        }
    }

    /**
     * Affiche le formulaire de création de menu
     */
    public function createMenu(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $themes = $this->themeRepo->findAll();
        $regimes = $this->regimeRepo->findAll();
        $plats = $this->platRepo->findAll();

        $platsByType = ['entrée' => [], 'plat' => [], 'dessert' => []];
        foreach ($plats as $plat) {
            $platsByType[$plat->getType()][] = $plat;
        }

        $title = 'Créer un menu';
        require __DIR__ . '/../../views/menu-create.php';
    }

    /**
     * Traite la création d'un menu
     */
    public function processCreateMenu(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'titre' => trim($_POST['titre'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'prix_base' => (float)($_POST['prix_base'] ?? 0),
            'nombre_personne_min' => (int)($_POST['nombre_personne_min'] ?? 0),
            'stock_disponible' => (int)($_POST['stock_disponible'] ?? 0),
            'theme_id' => (int)($_POST['theme_id'] ?? 0),
            'regime_id' => (int)($_POST['regime_id'] ?? 0),
            'conditions_particulieres' => trim($_POST['conditions_particulieres'] ?? '')
        ];
        $plats = $_POST['plats'] ?? [];

        try {
            $this->menuRepo->beginTransaction();
            $menuId = $this->menuRepo->save($data);
            if (!empty($plats)) {
                $this->menuRepo->linkPlats($menuId, $plats);
            }
            $this->menuRepo->commit();
            
            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=menu_cree#menus");
            exit;
        } catch (Exception $e) {
            $this->menuRepo->rollBack();
            header('Location: index.php?page=menu-create&error=erreur_serveur');
            exit;
        }
    }

    /**
     * Affiche le formulaire d'édition de menu
     */
    public function editMenu(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $menu = $this->menuRepo->findById($id);

        if (!$menu) {
            header('Location: index.php?page=espace-admin');
            exit;
        }

        $themes = $this->themeRepo->findAll();
        $regimes = $this->regimeRepo->findAll();
        $plats = $this->platRepo->findAll();
        $platsAssocies = $this->menuRepo->getPlatIds($id);

        $platsByType = ['entrée' => [], 'plat' => [], 'dessert' => []];
        foreach ($plats as $plat) {
            $platsByType[$plat->getType()][] = $plat;
        }

        $title = 'Modifier le menu';
        require __DIR__ . '/../../views/menu-edit.php';
    }

    /**
     * Traite l'édition d'un menu
     */
    public function processEditMenu(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['menu_id'] ?? 0);
        $data = [
            'titre' => trim($_POST['titre'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'prix_base' => (float)($_POST['prix_base'] ?? 0),
            'nombre_personne_min' => (int)($_POST['nombre_personne_min'] ?? 0),
            'stock_disponible' => (int)($_POST['stock_disponible'] ?? 0),
            'theme_id' => (int)($_POST['theme_id'] ?? 0),
            'regime_id' => (int)($_POST['regime_id'] ?? 0),
            'conditions_particulieres' => trim($_POST['conditions_particulieres'] ?? '')
        ];
        $plats = $_POST['plats'] ?? [];

        try {
            $this->menuRepo->beginTransaction();
            $this->menuRepo->update($id, $data);
            $this->menuRepo->unlinkPlats($id);
            if (!empty($plats)) {
                $this->menuRepo->linkPlats($id, $plats);
            }
            $this->menuRepo->commit();
            
            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=menu_modifie#menus");
            exit;
        } catch (Exception $e) {
            $this->menuRepo->rollBack();
            header("Location: index.php?page=menu-edit&id=$id&error=erreur_serveur");
            exit;
        }
    }

    /**
     * Traite la création d'un employé
     */
    public function createEmploye(): void {
        if (!$this->authService->isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'gsm' => '',
            'adresse_postale' => '',
            'ville' => '',
            'role' => 'employe',
            'actif' => 1
        ];

        if ($this->userRepo->save($data)) {
            header('Location: index.php?page=espace-admin&success=employe_cree#employes');
        } else {
            header('Location: index.php?page=espace-admin&error=save_failed#employes');
        }
        exit;
    }

    /**
     * Active/Désactive un employé
     */
    public function toggleEmploye(): void {
        if (!$this->authService->isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['employe_id'] ?? 0);
        $actif = (bool)($_POST['actif'] ?? 0);

        $this->userRepo->updateActif($id, $actif);
        header('Location: index.php?page=espace-admin&success=employe_mis_a_jour#employes');
        exit;
    }

    /**
     * Met à jour les horaires
     */
    public function updateHoraires(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $horaires = $_POST['horaire'] ?? [];
        foreach ($horaires as $id => $valeurs) {
            $this->horaireRepo->update((int)$id, $valeurs['ouverture'], $valeurs['fermeture']);
        }

        $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
        header("Location: index.php?page=$redirect&success=horaires_mis_a_jour#horaires");
        exit;
    }

    /**
     * Modère un avis
     */
    public function modererAvis(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['avis_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($action === 'valider') {
            $this->avisRepo->updateStatut($id, true);
        } else {
            $this->avisRepo->delete($id);
        }

        $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
        header("Location: index.php?page=$redirect&success=avis_modere#avis");
        exit;
    }

    /**
     * Supprime un menu
     */
    public function deleteMenu(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['menu_id'] ?? 0);
        
        // Vérification si des commandes actives y sont liées
        $menu = $this->menuRepo->findById($id);
        if (!$menu) {
             header('Location: index.php?page=espace-admin&error=menu_introuvable#menus');
             exit;
        }

        try {
            if ($this->menuRepo->delete($id)) {
                $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
                header("Location: index.php?page=$redirect&success=menu_supprime#menus");
            } else {
                header('Location: index.php?page=espace-admin&error=delete_failed#menus');
            }
        } catch (Exception $e) {
            header('Location: index.php?page=espace-admin&error=menu_commande_active#menus');
        }
        exit;
    }

    /**
     * Supprime un plat
     */
    public function deletePlat(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['plat_id'] ?? 0);

        try {
            if ($this->platRepo->delete($id)) {
                $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
                header("Location: index.php?page=$redirect&success=plat_supprime#menus");
            } else {
                header('Location: index.php?page=espace-admin&error=delete_failed#menus');
            }
        } catch (Exception $e) {
            header('Location: index.php?page=espace-admin&error=plat_utilise#menus');
        }
        exit;
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateCommandeStatut(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $commandeId = (int)($_POST['commande_id'] ?? 0);
        $nouveauStatut = $_POST['statut'] ?? '';
        $commentaire = $_POST['commentaire'] ?? '';

        if (!$commandeId || !$nouveauStatut) {
            header('Location: index.php?page=espace-employe&error=champs_manquants');
            exit;
        }

        try {
            $this->commandeRepo->beginTransaction();
            $this->commandeRepo->updateStatut($commandeId, $nouveauStatut);
            $this->commandeRepo->addSuivi($commandeId, $nouveauStatut, $commentaire);
            $this->commandeRepo->commit();

            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=statut_mis_a_jour#commandes");
        } catch (Exception $e) {
            $this->commandeRepo->rollBack();
            header('Location: index.php?page=espace-employe&error=erreur_serveur');
        }
        exit;
    }

    /**
     * Annule une commande (action employe)
     */
    public function annulerCommande(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $commandeId = (int)($_POST['commande_id'] ?? 0);
        $motif = $_POST['motif'] ?? 'Annulée par le personnel';

        if (!$commandeId) {
            header('Location: index.php?page=espace-employe&error=commande_invalide');
            exit;
        }

        try {
            $this->commandeRepo->beginTransaction();
            
            // Mise à jour statut
            $this->commandeRepo->updateStatut($commandeId, 'annulée');
            $this->commandeRepo->addSuivi($commandeId, 'annulée', $motif);
            
            // Remise en stock
            $commande = $this->commandeRepo->findById($commandeId);
            if ($commande) {
                $this->menuRepo->incrementStock($commande->getMenuId());
            }

            $this->commandeRepo->commit();
            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=commande_annulee#commandes");
        } catch (Exception $e) {
            $this->commandeRepo->rollBack();
            header('Location: index.php?page=espace-employe&error=erreur_serveur');
        }
        exit;
    }

    /**
     * API pour les statistiques (Admin Chart)
     */
    public function apiStats(): void {
        if (!$this->authService->isEmploye()) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        header('Content-Type: application/json');
        
        $filters = [
            'menu_id'    => $_GET['menu_id'] ?? null,
            'date_debut' => $_GET['date_debut'] ?? null,
            'date_fin'   => $_GET['date_fin'] ?? null
        ];

        $totals = $this->commandeRepo->getGlobalStats($filters);
        $chartRaw = $this->commandeRepo->getStatsByMenu($filters);

        $response = [
            'totals' => [
                'ca'  => (float)$totals['total_ttc'],
                'nb'  => (int)$totals['nb_commandes'],
                'moy' => (float)$totals['panier_moyen']
            ],
            'chart' => [
                'labels'    => array_column($chartRaw, 'titre'),
                'commandes' => array_map('intval', array_column($chartRaw, 'nombre_commandes')),
                'ca'        => array_map('floatval', array_column($chartRaw, 'ca'))
            ]
        ];

        echo json_encode($response);
        exit;
    }
}
