<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

/**
 * FRONT CONTROLLER - Vite & Gourmand
 * Toutes les requêtes passent par ici.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';

use App\Controller\HomeController;
use App\Controller\CommandeController;
use App\Controller\AuthController;
use App\Controller\MenuController;
use App\Controller\ContactController;
use App\Controller\UserController;
use App\Controller\LegalController;
use App\Controller\AdminController;
use App\Controller\EmployeController;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\HoraireRepository;
use App\Repository\AllergeneRepository;
use App\Repository\ThemeRepository;
use App\Repository\RegimeRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Service\AuthService;
use App\Service\CommandeService;
use App\Service\MailService;
use App\Service\LoggerService;
use App\Service\SecurityService;

// 1. Initialisation
$pdo = getDB();
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// 2. Initialisation des services partagés
$logger = new LoggerService();
$mailService = new MailService();
$securityService = new SecurityService();

// 3. Routage
try {
    switch ($page) {
        case 'home':
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $controller = new HomeController($menuRepo, $avisRepo);
            $controller->index();
            break;

        case 'commande':
            $menuRepo = new MenuRepository($pdo);
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $authService = new AuthService($userRepo);
            $commandeService = new CommandeService($commandeRepo, $menuRepo, $mailService);
            
            $controller = new CommandeController($menuRepo, $userRepo, $authService, $commandeService, $mailService);
            
            if ($action === 'create') {
                $controller->create();
            } else {
                $controller->index();
            }
            break;

        case 'menus':
            $menuRepo = new MenuRepository($pdo);
            $controller = new MenuController($menuRepo);
            
            if ($action === 'api') {
                $controller->apiList();
            } else {
                $controller->index();
            }
            break;

        case 'menu-detail':
            $menuRepo = new MenuRepository($pdo);
            $controller = new MenuController($menuRepo);
            $controller->detail();
            break;

        case 'login':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger, $userRepo, $mailService);
            
            if ($action === 'process') {
                $controller->login();
            } else {
                $controller->loginPage();
            }
            break;

        case 'forgot-password':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger, $userRepo, $mailService);
            $controller->forgotPassword();
            break;

        case 'reset-password':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger, $userRepo, $mailService);
            if ($action === 'process') {
                $controller->resetPassword();
            } else {
                $controller->resetPasswordPage();
            }
            break;

        case 'register':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger, $userRepo, $mailService);
            
            if ($action === 'process') {
                $controller->register();
            } else {
                $controller->registerPage();
            }
            break;

        case 'logout':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger, $userRepo, $mailService);
            $controller->logout();
            break;

        case 'contact':
            $controller = new ContactController($mailService, $logger);
            if ($action === 'submit') {
                $controller->submit();
            } else {
                $controller->index();
            }
            break;

        case 'espace-utilisateur':
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new UserController($userRepo, $commandeRepo, $avisRepo, $menuRepo, $authService, $logger);
            
            if ($action === 'create-avis') {
                $controller->createAvis();
            } elseif ($action === 'cancel-commande') {
                $controller->cancelCommande();
            } elseif ($action === 'update-profil') {
                $controller->updateProfil();
            } else {
                $controller->index();
            }
            break;

        case 'espace-admin':
        case 'espace-employe':
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $platRepo = new PlatRepository($pdo);
            $horaireRepo = new HoraireRepository($pdo);
            $allergeneRepo = new AllergeneRepository($pdo);
            $themeRepo = new ThemeRepository($pdo);
            $regimeRepo = new RegimeRepository($pdo);
            $authService = new AuthService($userRepo);
            
            if ($page === 'espace-employe' && ($action === 'index' || empty($action))) {
                $controller = new EmployeController($commandeRepo, $menuRepo, $avisRepo, $platRepo, $horaireRepo, $authService);
                $controller->index();
            } else {
                $controller = new AdminController($commandeRepo, $userRepo, $menuRepo, $avisRepo, $platRepo, $horaireRepo, $allergeneRepo, $themeRepo, $regimeRepo, $authService);
                
                if ($action === 'create-employe') {
                    $controller->createEmploye();
                } elseif ($action === 'toggle-employe') {
                    $controller->toggleEmploye();
                } elseif ($action === 'update-horaires') {
                    $controller->updateHoraires();
                } elseif ($action === 'moderer-avis') {
                    $controller->modererAvis();
                } elseif ($action === 'delete-menu') {
                    $controller->deleteMenu();
                } elseif ($action === 'delete-plat') {
                    $controller->deletePlat();
                } elseif ($action === 'update-commande-statut') {
                    $controller->updateCommandeStatut();
                } elseif ($action === 'annuler-commande') {
                    $controller->annulerCommande();
                } elseif ($action === 'api-stats') {
                    $controller->apiStats();
                } else {
                    $controller->index();
                }
            }
            break;

        case 'plat-create':
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $platRepo = new PlatRepository($pdo);
            $horaireRepo = new HoraireRepository($pdo);
            $allergeneRepo = new AllergeneRepository($pdo);
            $themeRepo = new ThemeRepository($pdo);
            $regimeRepo = new RegimeRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AdminController($commandeRepo, $userRepo, $menuRepo, $avisRepo, $platRepo, $horaireRepo, $allergeneRepo, $themeRepo, $regimeRepo, $authService);
            
            if ($action === 'process') {
                $controller->processCreatePlat();
            } else {
                $controller->createPlat();
            }
            break;

        case 'menu-create':
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $platRepo = new PlatRepository($pdo);
            $horaireRepo = new HoraireRepository($pdo);
            $allergeneRepo = new AllergeneRepository($pdo);
            $themeRepo = new ThemeRepository($pdo);
            $regimeRepo = new RegimeRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AdminController($commandeRepo, $userRepo, $menuRepo, $avisRepo, $platRepo, $horaireRepo, $allergeneRepo, $themeRepo, $regimeRepo, $authService);
            
            if ($action === 'process') {
                $controller->processCreateMenu();
            } else {
                $controller->createMenu();
            }
            break;

        case 'menu-edit':
            $userRepo = new UserRepository($pdo);
            $commandeRepo = new CommandeRepository($pdo);
            $menuRepo = new MenuRepository($pdo);
            $avisRepo = new AvisRepository($pdo);
            $platRepo = new PlatRepository($pdo);
            $horaireRepo = new HoraireRepository($pdo);
            $allergeneRepo = new AllergeneRepository($pdo);
            $themeRepo = new ThemeRepository($pdo);
            $regimeRepo = new RegimeRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AdminController($commandeRepo, $userRepo, $menuRepo, $avisRepo, $platRepo, $horaireRepo, $allergeneRepo, $themeRepo, $regimeRepo, $authService);
            
            if ($action === 'process') {
                $controller->processEditMenu();
            } else {
                $controller->editMenu();
            }
            break;
        
        case 'cgv':
            $controller = new LegalController();
            $controller->cgv();
            break;
        
        case 'mentions':
            $controller = new LegalController();
            $controller->mentions();
            break;

        default:
            if ($page !== 'home') {
                $file = $page . '.php';
                if (file_exists($file)) {
                    require $file;
                    exit;
                }
            }
            header('HTTP/1.0 404 Not Found');
            echo "Page non trouvée";
            break;
    }
} catch (Exception $e) {
    $logger->log('critical_error', ['message' => $e->getMessage(), 'page' => $page]);
    echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
}
