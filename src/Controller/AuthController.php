<?php
declare(strict_types=1);
namespace Controller;
use Core\Controller;
use Core\Session;
use Service\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function loginForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }
        $this->render('auth/login', [
            'csrf_token' => Session::generateCsrfToken(),
            'errors'     => Session::getFlash('errors') ?? [],
        ]);
    }

    public function login(): void
    {
        $this->verifyCsrf();
        $email    = trim($this->post('email'));
        $password = $this->post('password');
        try {
            $user = $this->authService->login($email, $password);
            if ($user === null) {
                Session::setFlash('errors', ['Email ou mot de passe incorrect.']);
                $this->redirect('/connexion');
            }
            $redirect = Session::get('redirect_after_login');
            Session::remove('redirect_after_login');
            if ($redirect) {
                $this->redirect($redirect);
            }
            $this->redirect($user->isAdmin() ? '/admin' : '/');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/connexion');
        }
    }

    public function registerForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }
        $this->render('auth/register', [
            'csrf_token' => Session::generateCsrfToken(),
            'errors'     => Session::getFlash('errors') ?? [],
        ]);
    }

    public function register(): void
    {
        $this->verifyCsrf();
        try {
            $this->authService->register($_POST);
            $this->redirect('/connexion');
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/inscription');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/connexion');
    }
}