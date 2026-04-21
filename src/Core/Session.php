<?php

declare(strict_types=1);

namespace Core;

/* Gestion  sessions PHP + token CSRF */

class Session
{
    private const TIMEOUT = 1800; 

    /* Démarre session cookie params */

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isProduction = Env::get('APP_ENV', 'development') === 'production';

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isProduction, // HTTPS uniquement en prod
            'httponly' => true,           // Inaccessible au JavaScript (anti-XSS)
            'samesite' => 'Strict',
        ]);

        session_name('QA_SESSION');
        session_start();

        self::checkTimeout();
    }

    /* Destruction session */

    private static function checkTimeout(): void
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::TIMEOUT) {
                self::destroy();
                return;
            }
        }

        $_SESSION['last_activity'] = time();
    }

    /* Définit une valeur en session */

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /* Récupère une valeur de session */

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }


    /* Supprime une clé de session */

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /* Détruit complètement la session */

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();

        // Supprime cookie de session

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
    }

    /* Régénère l'ID de session (à appeler à la connexion) */

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    // Gestion des messages flash (succès+erreur)

    public static function setFlash(string $type, mixed $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(string $type): mixed
    {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    //Tokens CSRF
  
   public static function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

    /* Check token CSRF */

    public static function verifyCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // Helpers d'authentification

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /* Retourne ID user */
    
    public static function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
