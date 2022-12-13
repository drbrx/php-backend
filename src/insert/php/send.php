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

    $values = "";
    $fieldList = "";
    foreach ($stmtResponseRows as $currentRecordRows) {
        if ($currentRecordRows["TABLE_SCHEMA"] == $_SESSION['db_name']) {
            if ($currentRecordRows["EXTRA"] != "auto_increment") {
                if ($fieldList != "") {
                    $fieldList .= ", ";
                    $values .= ", ";
                }
                $fieldList .= $currentRecordRows["COLUMN_NAME"];
                if (isset($configInfo[$currentRecordRows["COLUMN_NAME"]]) && ($configInfo[$currentRecordRows["COLUMN_NAME"]] == "radio" || $configInfo[$currentRecordRows["COLUMN_NAME"]] == "select")) {
                    $values .= "'" . $_REQUEST[$currentRecordRows["COLUMN_NAME"]] - 1 . "'";
                } else if (isset($configInfo[$currentRecordRows["COLUMN_NAME"]]) && $configInfo[$currentRecordRows["COLUMN_NAME"]] == "checkbox") {
                    $values .= "'" . (isset($_REQUEST["camposn"]) ? "s" : "n") . "'";
                } else {
                    $values .= "'" . $_REQUEST[$currentRecordRows["COLUMN_NAME"]] . "'";
                }
            }
        }
    }

    echo "fields: " . $fieldList;
    echo "values: " . $values;

    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $stmt = $pdo->prepare("INSERT INTO " . $table . " (" . $fieldList . ") VALUES (" . $values . ")");
    $stmt->execute();
} else {
    echo "no data";
};

header("location: ../insert.php");
