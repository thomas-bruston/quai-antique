<?php

declare(strict_types=1);

namespace Service;

use Core\Session;
use Entity\User;
use Repository\UserRepository;

/* Authentification */

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Connexion 

    public function login(string $email, string $password): ?User
    {
        if (empty($email) || empty($password)) {
            return null;
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user->getPassword())) {
            return null;
        }

        $this->storeInSession($user);
        return $user;
    }

    private function storeInSession(User $user): void
    {
        Session::regenerate();

        Session::set('user_id', $user->getUserId());
        Session::set('user', [
            'id'     => $user->getUserId(),
            'prenom' => $user->getPrenom(),
            'nom'    => $user->getNom(),
            'email'  => $user->getEmail(),
            'role'   => $user->getRole(),
        ]);
    }

    // Déconnexion

    public function logout(): void
    {
        Session::destroy();
    }

    // Inscription

    public function register(array $data): int
    {
        $nom                  = trim($data['nom'] ?? '');
        $prenom               = trim($data['prenom'] ?? '');
        $email                = trim($data['email'] ?? '');
        $password             = $data['password'] ?? '';
        $passwordConfirm      = $data['password_confirm'] ?? '';
        $allergies            = trim($data['allergies'] ?? '') ?: null;
        $nombreConvivesDefaut = (int) ($data['nombre_convives_defaut'] ?? 1);

        if (empty($nom) || empty($prenom)) {
            throw new \InvalidArgumentException('Nom et prénom sont obligatoires.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Adresse email invalide.');
        }

        if ($this->userRepository->emailExists($email)) {
            throw new \RuntimeException('Cette adresse email est déjà utilisée.');
        }

        $passwordErrors = $this->validatePassword($password, $passwordConfirm);
        if (!empty($passwordErrors)) {
            throw new \InvalidArgumentException(implode(' ', $passwordErrors));
        }

        $user = new User(
            email:                $email,
            password:             password_hash($password, PASSWORD_BCRYPT),
            prenom:               $prenom,
            nom:                  $nom,
            nombreConvivesDefaut: $nombreConvivesDefaut,
            allergies:            $allergies,
            role:                 'client'
        );

        return $this->userRepository->create($user);
    }

    // Validation mot de passe

    public function validatePassword(string $password, string $confirm = ''): array
    {
        $errors = [];

        if (strlen($password) < 10) {
            $errors[] = 'Le mot de passe doit contenir au moins 10 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }
        if (!empty($confirm) && $password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }
}
