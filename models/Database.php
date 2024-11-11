<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'world_transfer';
    private $db_user = 'root';
    private $db_pass = '1111';

    public function getConnection()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';
        return new PDO($dsn, $this->db_user, $this->db_pass);
    }
}
