<?php

/**
 * FRONT CONTROLLER - Vite & Gourmand
 * Toutes les requêtes passent par ici.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

use App\Controller\HomeController;
use App\Controller\CommandeController;
use App\Controller\AuthController;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Service\AuthService;
use App\Service\CommandeService;
use App\Service\MailService;
use App\Service\LoggerService;

// 1. Initialisation
sessionStart();
$pdo = getDB();
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// 2. Initialisation des services partagés
$logger = new LoggerService();
$mailService = new MailService();

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

        case 'login':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger);
            
            if ($action === 'process') {
                $controller->login();
            } else {
                $controller->loginPage();
            }
            break;

        case 'register':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger);
            
            if ($action === 'process') {
                $controller->register();
            } else {
                $controller->registerPage();
            }
            break;

        case 'logout':
            $userRepo = new UserRepository($pdo);
            $authService = new AuthService($userRepo);
            $controller = new AuthController($authService, $logger);
            $controller->logout();
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
