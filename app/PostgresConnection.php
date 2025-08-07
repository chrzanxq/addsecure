<?php

namespace App;

use PDO;

class PostgresConnection
{
    private ?PDO $pdo = null;

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = 'pgsql:host=localhost;port=5432;dbname=addsecure;user=*;password=*';
            $this->pdo = new PDO($dsn);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->pdo;
    }
}
