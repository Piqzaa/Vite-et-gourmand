<?php
namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\ThemeRepository;
use App\Repository\RegimeRepository;
use App\Service\AuthService;
use Exception;

class GestionMenuController {
    public function __construct(
        private MenuRepository $menuRepo,
        private PlatRepository $platRepo,
        private ThemeRepository $themeRepo,
        private RegimeRepository $regimeRepo,
        private AuthService $authService
    ) {}

    public function create(): void {
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

    public function processCreate(): void {
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

    public function edit(): void {
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

    public function processEdit(): void {
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

    public function delete(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['menu_id'] ?? 0);

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
}
