<?php
include_once("../common/php/DBConnector.php");

$db = $_SESSION['db'];
$stmt = $db->prepare("SELECT * FROM `?`");
$table = 'tprodotti';
$stmt->execute([$table]);
$stmtResponse = $stmt->fetchAll();

foreach ($stmtResponse as $currentRecord) {
    echo $currentRecord['name'];
}
