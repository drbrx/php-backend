Elaborazione in corso...
Attendere...
<?php
require_once("../../common/php/DBConnector.php");
if (isset($_REQUEST)) {
    //echo var_dump($_REQUEST);

    $table = $_SESSION['table_name'];

    $connMySQLRows = new ConnectionMySQL();
    $pdoRows = $connMySQLRows->getConnection();
    $stmtRows = $pdoRows->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
    $stmtRows->execute();
    $stmtResponseRows = $stmtRows->fetchAll();

    $assignments = "";
    foreach ($stmtResponseRows as $currentRecordRows) {
        if ($currentRecordRows["TABLE_SCHEMA"] == $_SESSION['db_name']) {
            if ($currentRecordRows["EXTRA"] != "auto_increment") {
                if ($assignments != "") {
                    $assignments .= ", ";
                }
                if (isset($configInfo[$currentRecordRows["COLUMN_NAME"]]) && ($configInfo[$currentRecordRows["COLUMN_NAME"]] == "radio" || $configInfo[$currentRecordRows["COLUMN_NAME"]] == "select")) {
                    $assignments .= $currentRecordRows["COLUMN_NAME"] . "='" . $_REQUEST[$currentRecordRows["COLUMN_NAME"]] - 1 . "' ";
                } else if (isset($configInfo[$currentRecordRows["COLUMN_NAME"]]) && $configInfo[$currentRecordRows["COLUMN_NAME"]] == "checkbox") {
                    $assignments .= $currentRecordRows["COLUMN_NAME"] . "='" . (isset($_REQUEST["camposn"]) ? "s" : "n") . "' ";
                } else {
                    $assignments .= $currentRecordRows["COLUMN_NAME"] . "='" . $_REQUEST[$currentRecordRows["COLUMN_NAME"]] . "' ";
                }
            }
        }
    }
    echo "assigning: " . $assignments;

    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $stmt = $pdo->prepare("UPDATE " . $table . " SET " . $assignments . "WHERE id = '" . $_REQUEST['id'] . "'");
    $stmt->execute();
} else {
    echo "no data";
};

header("location: ../details.php?id=" . $_REQUEST['id']);
