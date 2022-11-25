<?php
require_once("../common/php/DBConnector.php");


$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = $_SESSION['table_name'];
$stmt = $pdo->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
$stmt->execute();
$stmtResponse = $stmt->fetchAll();
?>

<form action="php/send.php">
    <table>

        <?php
        foreach ($stmtResponse as $currentRecord) {
            if ($currentRecord["TABLE_SCHEMA"] == $_SESSION['db_name']) {
                //echo var_dump($currentRecord);
                if ($currentRecord["EXTRA"] != "auto_increment") {
                    echo "<td>" . $currentRecord["COLUMN_NAME"] . "</td><td>";
                    $maxLenght = preg_replace("/[^0-9]/", "", $currentRecord["COLUMN_TYPE"]);
                    $dataType = strtok($currentRecord["COLUMN_TYPE"], '(');
                    //echo $currentRecord["IS_NULLABLE"];

                    switch (isset($configInfo[$currentRecord["COLUMN_NAME"]]) ? $configInfo[$currentRecord["COLUMN_NAME"]] : $dataType) {
                        case "int":
                            echo "<input type=\"text\" pattern=\"\d*\" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" placeholder=\"numero di max " . $maxLenght . " cifre\" " . ($currentRecord["IS_NULLABLE"] != "NO" ? "" : "required") . "></input>";
                            break;
                        case "varchar":
                            echo ($maxLenght > 20 ? "<textarea" : "<input type=\"text\"") .
                                " \" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" value=\"\" placeholder=\"testo di max " . $maxLenght . " caratteri\" " . ($currentRecord["IS_NULLABLE"] != "NO" ? "" : "required") . ">" . ($maxLenght > 20 ? "</textarea>" : "</input>");
                            break;
                        case "date":
                            echo "<input type=\"date\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" " . ($currentRecord["IS_NULLABLE"] != "NO" ? "" : "required") . "></input>";
                            break;
                        case "customText":
                            echo "<input type=\"text\" maxlength=\"" . $configInfo[$currentRecord["COLUMN_NAME"] . 'Length'] . "\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" placeholder=\"stringa di max " . $configInfo[$currentRecord["COLUMN_NAME"] . 'Length'] . " caratteri\" " . ($currentRecord["IS_NULLABLE"] != "NO" ? "" : "required") . "></input>";
                            break;
                        case "checkbox":
                            echo "<input type=\"checkbox\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\"></input>";
                            break;
                        case "select":
                            $foreignTable = getForeignValues(strtolower(str_replace("id", '', $currentRecord["COLUMN_NAME"])), $configInfo);
                            echo "<select name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" " . ($currentRecord["IS_NULLABLE"] != "NO" ? "" : "required") . ">";
                            foreach ($foreignTable as $foreignRow) {
                                echo "<option value=\"" . $foreignRow['id'] . "\">" . $foreignRow[$configInfo['t' . strtolower(str_replace("id", '', $currentRecord["COLUMN_NAME"])) . 'MAINFIELD']] . "</option>";
                            }
                            echo "</select>";
                            break;
                        case "radio":
                            $foreignTable = getForeignValues(strtolower(str_replace("id", '', $currentRecord["COLUMN_NAME"])), $configInfo);
                            for ($i = 0; $i < count($foreignTable); $i++) {
                                echo "<input type=\"radio\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . $i . "\" value=\"" . $foreignTable[$i]['id'] . "\">" . $foreignTable[$i][$configInfo['t' . strtolower(str_replace("id", '', $currentRecord["COLUMN_NAME"])) . 'MAINFIELD']] . "</input>";
                            }

                            break;
                    }
                }
                echo "</td></tr>";
            }
        }
        ?>
        <tr>
            <td></td>
            <td><input type="submit" name="submit" value="Invia" /></td>
        </tr>
    </table>
</form>

<?php
function getForeignValues($tableName, $configInfo)
{
    $connMySQL = new ConnectionMySQL();
    $pdo = $connMySQL->getConnection();
    $foreignTableStmt = $pdo->prepare("SELECT id, " . $configInfo['t' . $tableName . 'MAINFIELD'] . " FROM " . $tableName);
    $foreignTableStmt->execute();
    $foreignTableStmtResponse = $foreignTableStmt->fetchAll();

    //echo var_dump($foreignTableStmtResponse);
    return $foreignTableStmtResponse;
}
