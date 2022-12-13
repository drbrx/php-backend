<?php
session_start();

$config = fopen(__DIR__ . "\config.cfg", "r");
if ($config != false) {
    $configInfo = array();
    while (!feof($config)) {
        $currentLine = fgets($config);
        if ((!preg_match("/^\s*\/\//", $currentLine))) {
            $lineResult = array();
            $lineResult = explode(": ", $currentLine);
            $configInfo += [$lineResult[0] => preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $lineResult[1])];
        }
    }
    //echo var_dump($configInfo);
    fclose($config);
}

$_SESSION['db_name'] = $configInfo['dbName'];
$_SESSION['table_name'] = $configInfo['tabName'];
$_SESSION['rowsPerPage'] = $configInfo['rowsPerPage'];
class ConnectionMySQL
{

    private const host = '127.0.0.1:3306';
    private const db = 'backoffice';
    private const user = 'root';
    private const pass = '';
    private const charset = 'utf8mb4';
    private const options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true,
    ];
    private $connection = null;
    private $dsn = null;

    function __construct()
    {
        try {
            $this->dsn = "mysql:host=" . ConnectionMySQL::host . ";dbname=" . (isset($_SESSION['db_name']) ? $_SESSION['db_name'] : ConnectionMySQL::db) . ";charset=" . ConnectionMySQL::charset;
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
