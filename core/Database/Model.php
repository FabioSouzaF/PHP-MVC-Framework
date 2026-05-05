<?php

namespace Core\Database;

use PDO;

abstract class Model
{
    protected ?PDO $db = null;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }
}
