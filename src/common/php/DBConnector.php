<?php
session_start();
$_SESSION['db_name'] = 'backoffice';
class ConnectionMySQL
{

    private const host = '127.0.0.1:3306';
    private const db = 'backoffice';
    private const user = 'root';
    private const pass = '';
    private const charset = 'utf8mb4';
    private $dsn = "mysql:host=" . ConnectionMySQL::host . ";dbname=" . ConnectionMySQL::db . ";charset=" . ConnectionMySQL::charset;
    private const options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true,
    ];
    private $connection = null;

    function __construct()
    {
        try {
            $this->connection = new PDO($this->dsn, ConnectionMySQL::user, ConnectionMySQL::pass, ConnectionMySQL::options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }
    }
}
