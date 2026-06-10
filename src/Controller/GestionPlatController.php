<?php
namespace App\Controller;

use App\Repository\PlatRepository;
use App\Repository\AllergeneRepository;
use App\Service\AuthService;
use App\Service\FileService;
use App\Service\SecurityService;
use Exception;

class GestionPlatController {
    public function __construct(
        private PlatRepository $platRepo,
        private AllergeneRepository $allergeneRepo,
        private AuthService $authService,
        private FileService $fileService,
        private SecurityService $securityService
    ) {}

    public function create(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $allergenes = $this->allergeneRepo->findAll();
        $title = 'Créer un plat';
        
        require __DIR__ . '/../../views/plat-create.php';
    }

    public function processCreate(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=plat-create&error=csrf_invalid');
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

        $imagePath = null;
        try {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                header('Location: index.php?page=plat-create&error=image_manquante');
                exit;
            }

            $imagePath = $this->fileService->uploadImage($_FILES['image'], 'plat_');
        } catch (Exception $e) {
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

    public function delete(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-admin&error=csrf_invalid#menus');
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
}
