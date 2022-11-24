<?php
require_once("../common/php/DBConnector.php");
$config = fopen("../../config.cfg", "r");
if ($config != false) {
    $configInfo = array();
    while (!feof($config)) {
        $lineResult = array();
        $lineResult = explode(": ", fgets($config));
        $configInfo += [$lineResult[0] => preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $lineResult[1])];
    }
    //echo var_dump($configInfo);
    fclose($config);
}

$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = 'tabella';
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
                    }
                }
                echo "</td></tr>";
            }
        }
        ?>
        <tr>
            <td>Submit</td>
            <td><input type="submit" name="submit" value="Invia" /></td>
        </tr>
    </table>
</form>