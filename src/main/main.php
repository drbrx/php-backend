<?php
require_once("../common/php/DBConnector.php");

$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = 'tabella';
$stmt = $pdo->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
$stmt->execute();
$stmtResponse = $stmt->fetchAll();

foreach ($stmtResponse as $currentRecord) {
    if ($currentRecord["TABLE_SCHEMA"] == $_SESSION['db_name']) {
        echo var_dump($currentRecord) . "<br></br>";
        echo $currentRecord["COLUMN_NAME"] . " > " . $currentRecord["COLUMN_TYPE"] . "<br></br>";
        if(substr($currentRecord["TABLE_SCHEMA"], 0, 2) == "id"){
            echo "foreign";
        }
    }
}
