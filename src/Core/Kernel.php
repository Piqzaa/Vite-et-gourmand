<?php

namespace App\Core;

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
use App\Service\FileService;
use App\Service\RateLimiter;
use App\Controller\HomeController;
use App\Controller\CommandeController;
use App\Controller\AuthController;
use App\Controller\MenuController;
use App\Controller\ContactController;
use App\Controller\UserController;
use App\Controller\LegalController;
use App\Controller\AdminController;
use App\Controller\EmployeController;
use App\Controller\GestionPlatController;
use App\Controller\GestionMenuController;
use App\Controller\GestionCommandeController;
use App\Controller\GestionEmployeController;
use App\Controller\HoraireController;
use App\Controller\AvisModerationController;
use App\Controller\StatsController;

class Kernel
{
    private Container $container;
    private Router $router;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();
        $this->boot();
    }

    private function boot(): void
    {
        $this->container->set(\PDO::class, \getDB());

        $this->registerServices();
        $this->registerRepositories();
        $this->registerRoutes();
    }

    private function registerServices(): void
    {
        $simpleServices = [
            LoggerService::class,
            MailService::class,
            SecurityService::class,
            RateLimiter::class,
        ];
        foreach ($simpleServices as $s) {
            $this->container->factory($s, fn() => new $s());
        }

        $this->container->factory(FileService::class, fn() => new FileService(__DIR__ . '/../../assets/img/plats/'));

        $this->container->factory(AuthService::class, fn(Container $c) => new AuthService($c->get(UserRepository::class)));

        $this->container->factory(CommandeService::class, fn(Container $c) => new CommandeService(
            $c->get(CommandeRepository::class),
            $c->get(MenuRepository::class),
            $c->get(MailService::class),
        ));
    }

    private function registerRepositories(): void
    {
        $repos = [
            MenuRepository::class,
            PlatRepository::class,
            HoraireRepository::class,
            AllergeneRepository::class,
            ThemeRepository::class,
            RegimeRepository::class,
            UserRepository::class,
            CommandeRepository::class,
            AvisRepository::class,
        ];
        foreach ($repos as $r) {
            $this->container->factory($r, fn(Container $c) => new $r($c->get(\PDO::class)));
        }
    }

    private function registerRoutes(): void
    {
        $this->router->add('home', new Route(HomeController::class));
        $this->router->add('commande', new Route(CommandeController::class, actionMap: ['create' => 'create']));
        $this->router->add('menus', new Route(MenuController::class, actionMap: ['api' => 'apiList']));
        $this->router->add('menu-detail', new Route(MenuController::class, defaultAction: 'detail'));
        $this->router->add('login', new Route(AuthController::class, defaultAction: 'loginPage', actionMap: ['process' => 'login']));
        $this->router->add('forgot-password', new Route(AuthController::class, defaultAction: 'forgotPassword'));
        $this->router->add('reset-password', new Route(AuthController::class, defaultAction: 'resetPasswordPage', actionMap: ['process' => 'resetPassword']));
        $this->router->add('register', new Route(AuthController::class, defaultAction: 'registerPage', actionMap: ['process' => 'register']));
        $this->router->add('logout', new Route(AuthController::class, defaultAction: 'logout'));
        $this->router->add('contact', new Route(ContactController::class, actionMap: ['submit' => 'submit']));
        $this->router->add('espace-utilisateur', new Route(UserController::class, actionMap: [
            'create-avis' => 'createAvis',
            'cancel-commande' => 'cancelCommande',
            'update-profil' => 'updateProfil',
        ]));

        $this->router->add('plat-create', new Route(GestionPlatController::class, defaultAction: 'create', actionMap: ['process' => 'processCreate']));
        $this->router->add('menu-create', new Route(GestionMenuController::class, defaultAction: 'create', actionMap: ['process' => 'processCreate']));
        $this->router->add('menu-edit', new Route(GestionMenuController::class, defaultAction: 'edit', actionMap: ['process' => 'processEdit']));

        $this->router->add('espace-admin', new Route(
            handler: function (Container $c, string $action) {
                if ($action === 'index' || $action === '') {
                    $c->resolve(AdminController::class)->index();
                    return;
                }

                $map = [
                    'create-employe'       => [GestionEmployeController::class, 'create'],
                    'toggle-employe'       => [GestionEmployeController::class, 'toggle'],
                    'update-horaires'      => [HoraireController::class, 'update'],
                    'moderer-avis'         => [AvisModerationController::class, 'moderer'],
                    'delete-menu'          => [GestionMenuController::class, 'delete'],
                    'delete-plat'          => [GestionPlatController::class, 'delete'],
                    'update-commande-statut' => [GestionCommandeController::class, 'updateStatut'],
                    'annuler-commande'     => [GestionCommandeController::class, 'annuler'],
                    'api-stats'            => [StatsController::class, 'api'],
                ];

                if (isset($map[$action])) {
                    [$controllerClass, $method] = $map[$action];
                    $c->resolve($controllerClass)->$method();
                }
            }
        ));

        $this->router->add('espace-employe', new Route(
            handler: function (Container $c, string $action) {
                if ($action === 'index' || $action === '') {
                    $c->resolve(EmployeController::class)->index();
                    return;
                }

                $map = [
                    'update-commande-statut' => [GestionCommandeController::class, 'updateStatut'],
                    'annuler-commande'       => [GestionCommandeController::class, 'annuler'],
                    'update-horaires'        => [HoraireController::class, 'update'],
                    'moderer-avis'           => [AvisModerationController::class, 'moderer'],
                    'delete-menu'            => [GestionMenuController::class, 'delete'],
                    'delete-plat'            => [GestionPlatController::class, 'delete'],
                ];

                if (isset($map[$action])) {
                    [$controllerClass, $method] = $map[$action];
                    $c->resolve($controllerClass)->$method();
                }
            }
        ));
        $this->router->add('cgv', new Route(LegalController::class, defaultAction: 'cgv'));
        $this->router->add('mentions', new Route(LegalController::class, defaultAction: 'mentions'));
    }

    public function handle(): void
    {
        $page = $_GET['page'] ?? 'home';
        $action = $_GET['action'] ?? 'index';
        $logger = $this->container->get(LoggerService::class);

        try {
            $route = $this->router->resolve($page);

            if ($route === null) {
                $this->handleNotFound($page);
                return;
            }

            if ($route->handler !== null) {
                ($route->handler)($this->container, $action);
                return;
            }

            $controller = $this->container->resolve($route->controllerClass);
            $method = $route->getAction($action);

            if (!method_exists($controller, $method)) {
                $this->handleNotFound($page);
                return;
            }

            $controller->$method();
        } catch (\Exception $e) {
            $logger->log('critical_error', ['message' => $e->getMessage(), 'page' => $page]);
            echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
        }
    }

    private function handleNotFound(string $page): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $page)) {
            header('HTTP/1.0 404 Not Found');
            echo "Page non trouvée";
            return;
        }
        $file = $page . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
        header('HTTP/1.0 404 Not Found');
        echo "Page non trouvée";
    }
}
