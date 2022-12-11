Elaborazione in corso...
Attendere...
<?php
require_once("../../common/php/DBConnector.php");
if (isset($_REQUEST)) {
    //echo var_dump($_REQUEST);
    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $table = $_SESSION['table_name'];
    $stmt = $pdo->prepare("UPDATE " . $table . " SET testo='" . $_REQUEST["testo"] . "', data='" . $_REQUEST["data"] . "', camposn='" . (isset($_REQUEST["camposn"]) ? "s" : "n") . "', numero='" . $_REQUEST["numero"] . "', percorso='" . $_REQUEST["percorso"] . "', idSupporto='" . $_REQUEST["idSupporto"] - 1 . "', idRadioet='" . $_REQUEST["idRadioet"] - 1 . "' WHERE id = '" . $_REQUEST['id'] . "'");
    $stmt->execute();
} else {
    echo "no data";
};

header("location: ../details.php?id=" . $_REQUEST['id']);
