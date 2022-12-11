<?php
require_once("../../common/php/DBConnector.php");
if (isset($_REQUEST)) {
    //echo var_dump($_REQUEST);
    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $table = $_SESSION['table_name'];
    $stmt = $pdo->prepare("DELETE FROM " . $table . " WHERE id = '" . $_REQUEST['id'] . "'");
    $stmt->execute();
    echo "Element deleted succesfully.";
} else {
    echo "no data";
};
