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
        private AuthService $authService
    ) {}

    public function index(): void {
        if (!$this->authService->isAdmin() && !$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $commandes = $this->commandeRepo->findAllWithDetails();
        $employes = $this->userRepo->findByRole('employe');
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $statsCA = $this->commandeRepo->getGlobalStats();
        $statsParMenu = $this->commandeRepo->getStatsByMenu();
        $horaires = $this->horaireRepo->findAll();

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

        // Gestion de l'upload d'image
        $uploadDir = __DIR__ . '/../../assets/img/plats/';
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];

            if (!in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
                header('Location: index.php?page=plat-create&error=format_invalide');
                exit;
            }

            if ($fileSize > 2 * 1024 * 1024) {
                header('Location: index.php?page=plat-create&error=fichier_trop_lourd');
                exit;
            }

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('plat_', true) . '.' . $extension;
            
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFileName)) {
                header('Location: index.php?page=plat-create&error=upload_echoue');
                exit;
            }
            $imagePath = $newFileName;
        } else {
            header('Location: index.php?page=plat-create&error=image_manquante');
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
            if ($imagePath && file_exists($uploadDir . $imagePath)) unlink($uploadDir . $imagePath);
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
            $platsByType[$plat['type']][] = $plat;
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
            $platsByType[$plat['type']][] = $plat;
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
}
