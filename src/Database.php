<?php

namespace App;

use \PDO;

class Database
{
    /* Constructor with params to connect to DB */
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {
    }

    public function getConnection(): PDO
    {
        /* Connect */
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        /* PDO convert all to String, so stringify to false */
        return new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}
