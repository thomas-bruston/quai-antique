<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;
use PDO;


/* Classe abstraite Repository, base commune à tous les repositories */

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
}
