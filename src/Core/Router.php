<?php

namespace Core;

use Controller\AuthController;
use Controller\HomeController;
use Controller\DishController;
use Controller\GalleryController;
use Controller\ReservationController;
use Controller\ContactController;
use Controller\UserController;
use Controller\AdminController;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    // routes

    private function registerRoutes(): void
    {
        // Routes visiteur

        $this->add('GET',  '/',                                     HomeController::class,        'index');
        $this->add('GET',  '/mentions-legales',                     HomeController::class,        'mentions');

        $this->add('GET',  '/carte',                                DishController::class,        'index');

        $this->add('GET',  '/galerie',                              GalleryController::class,     'index');

        $this->add('GET',  '/reservation',                          ReservationController::class, 'form');
        $this->add('POST', '/reservation',                          ReservationController::class, 'store');
        $this->add('GET',  '/reservation/check-availability',       ReservationController::class, 'checkAvailability');

        $this->add('GET',  '/contact',                              ContactController::class,     'form');
        $this->add('POST', '/contact',                              ContactController::class,     'store');

        $this->add('GET',  '/connexion',                            AuthController::class,        'loginForm');
        $this->add('POST', '/connexion',                            AuthController::class,        'login');
        $this->add('GET',  '/inscription',                          AuthController::class,        'registerForm');
        $this->add('POST', '/inscription',                          AuthController::class,        'register');
        $this->add('GET',  '/deconnexion',                          AuthController::class,        'logout');

        // Routes user

        $this->add('GET',  '/mon-compte',                                       UserController::class, 'reservations',      'client');
        $this->add('GET',  '/mon-compte/reservations',                          UserController::class, 'reservations',      'client');
        $this->add('GET',  '/mon-compte/reservation/{id}/modifier',             UserController::class, 'editReservation',   'client');
        $this->add('POST', '/mon-compte/reservation/{id}/modifier',             UserController::class, 'updateReservation', 'client');
        $this->add('POST', '/mon-compte/reservation/{id}/supprimer',            UserController::class, 'deleteReservation', 'client');
        $this->add('GET',  '/mon-compte/profil',                                UserController::class, 'profile',           'client');
        $this->add('POST', '/mon-compte/profil',                                UserController::class, 'updateProfile',     'client');
        $this->add('POST', '/mon-compte/supprimer',                             UserController::class, 'deleteAccount',     'client');

        // Routes admin 

        $this->add('GET',  '/admin',                                AdminController::class,   'dashboard',         'admin');
        $this->add('GET',  '/admin/parametres',                     AdminController::class,   'settings',          'admin');
        $this->add('POST', '/admin/parametres',                     AdminController::class,   'updateSettings',    'admin');

        $this->add('GET',  '/admin/reservations',                   AdminController::class,   'reservations',      'admin');
        $this->add('POST', '/admin/reservation/{id}/modifier',      AdminController::class,   'updateReservation', 'admin');
        $this->add('POST', '/admin/reservation/{id}/supprimer',     AdminController::class,   'deleteReservation', 'admin');

        $this->add('GET',  '/admin/carte',                          DishController::class,    'admin',             'admin');
        $this->add('POST', '/admin/plat/ajouter',                   DishController::class,    'storeDish',         'admin');
        $this->add('POST', '/admin/plat/{id}/modifier',             DishController::class,    'updateDish',        'admin');
        $this->add('POST', '/admin/plat/{id}/supprimer',            DishController::class,    'deleteDish',        'admin');
        $this->add('POST', '/admin/menu/ajouter',                   DishController::class,    'storeMenu',         'admin');
        $this->add('POST', '/admin/menu/{id}/modifier',             DishController::class,    'updateMenu',        'admin');
        $this->add('POST', '/admin/menu/{id}/supprimer',            DishController::class,    'deleteMenu',        'admin');

        $this->add('GET',  '/admin/galerie',                        GalleryController::class, 'admin',             'admin');
        $this->add('POST', '/admin/galerie/ajouter',                GalleryController::class, 'store',             'admin');
        $this->add('POST', '/admin/galerie/{id}/modifier',          GalleryController::class, 'update',            'admin');
        $this->add('POST', '/admin/galerie/{id}/supprimer',         GalleryController::class, 'delete',            'admin');

        $this->add('GET',  '/admin/messages',                       ContactController::class, 'index',             'admin');
        $this->add('POST', '/admin/messages/{id}/supprimer',        ContactController::class, 'delete',            'admin');
    }

    // Ajout route

    private function add(string $method, string $path, string $controller, string $action, ?string $requiredRole = null): void
    {
        $this->routes[] = [
            'method'       => $method,
            'path'         => $path,
            'controller'   => $controller,
            'action'       => $action,
            'requiredRole' => $requiredRole,
        ];
    }

    // Dispatch

    public function dispatch(string $method, string $uri): void
    {
        $uri = strtok($uri, '?'); // Ignorer les query strings

        foreach ($this->routes as $route) {
            $params = [];

            if ($route['method'] !== $method) {
                continue;
            }

            if (!$this->match($route['path'], $uri, $params)) {
                continue;
            }

            // Contrôle accès 

            if ($route['requiredRole'] !== null) {
                $this->checkAccess($route['requiredRole']);
            }

            // Instanciation et appel du controller 

            $controllerClass = $route['controller'];
            $controller      = new $controllerClass();
            $action          = $route['action'];

            $controller->$action(...array_values($params));
            return;
        }

    // Erreur 404

    http_response_code(404);
    require_once __DIR__ . '/../../templates/errors/404.php';

    }

    // Matching

    private function match(string $routePath, string $uri, array &$params): bool
    {
        $pattern = preg_replace('/\{[a-z]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Supprimer le match complet
            $params = $matches;
            return true;
        }

        return false;
    }

    // Contrôle accès avec rôle

    private function checkAccess(string $requiredRole): void
    {
        $user = \Core\Session::get('user');

        if ($user === null) {
            header('Location: /connexion');
            exit;
        }

        if ($requiredRole === 'admin' && $user['role'] !== 'admin') {
            http_response_code(403);
            require_once __DIR__ . '/../../templates/errors/403.php';
            exit;
        }

        if ($requiredRole === 'client' && !in_array($user['role'], ['client', 'admin'], true)) {
            http_response_code(403);
            require_once __DIR__ . '/../../templates/errors/403.php';
            exit;
        }
    }
}
