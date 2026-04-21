<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires — Validation mot de passe
 * CP7 : style défensif, tests unitaires
 */
class PasswordValidatorTest extends TestCase
{
    private function validate(string $password, string $confirm = ''): array
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

    public function testMotDePasseTropCourt(): void
    {
        $errors = $this->validate('Ab1!');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('10 caractères', $errors[0]);
    }

    public function testMotDePasseSansMajuscule(): void
    {
        $errors = $this->validate('abcdefgh1!');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('majuscule', $errors[0]);
    }

    public function testMotDePasseSansMinuscule(): void
    {
        $errors = $this->validate('ABCDEFGH1!');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('minuscule', $errors[0]);
    }

    public function testMotDePasseSansChiffre(): void
    {
        $errors = $this->validate('Abcdefghi!');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('chiffre', $errors[0]);
    }

    public function testMotDePasseSansCaractereSpecial(): void
    {
        $errors = $this->validate('Abcdefgh12');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('caractère spécial', $errors[0]);
    }

    public function testMotDePasseValide(): void
    {
        $errors = $this->validate('Admin@12345');
        $this->assertEmpty($errors);
    }

    public function testMotDePasseValideAvecConfirmation(): void
    {
        $errors = $this->validate('Admin@12345', 'Admin@12345');
        $this->assertEmpty($errors);
    }

    public function testConfirmationDifferente(): void
    {
        $errors = $this->validate('Admin@12345', 'Autre@12345');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('correspondent pas', end($errors));
    }

    public function testPlusieursErreursRetournees(): void
    {
        $errors = $this->validate('abc');
        $this->assertGreaterThan(1, count($errors));
    }
}
