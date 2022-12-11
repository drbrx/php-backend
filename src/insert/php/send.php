Elaborazione in corso...
Attendere...
<?php
require_once("../../common/php/DBConnector.php");
if (isset($_REQUEST)) {
    //echo var_dump($_REQUEST);
    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $table = 'tabella';
    $stmt = $pdo->prepare("INSERT INTO tabella(testo, data, camposn, numero, percorso, idSupporto, idRadioet) VALUES ('" . $_REQUEST["testo"] . "','" . $_REQUEST["data"] . "','" . (isset($_REQUEST["camposn"]) ? "s" : "n") . "','" . $_REQUEST["numero"] . "','" . $_REQUEST["percorso"] . "','" . $_REQUEST["idSupporto"] . "','" . $_REQUEST["idRadioet"] . "')");
    $stmt->execute();
} else {
    echo "no data";
};

header("location: ../insert.php");
