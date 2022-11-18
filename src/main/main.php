<?php
require_once("../common/php/DBConnector.php");

$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = 'tabella';
$stmt = $pdo->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
$stmt->execute();
$stmtResponse = $stmt->fetchAll();
?>

<form>
    <table>

        <?php
        foreach ($stmtResponse as $currentRecord) {
            if ($currentRecord["TABLE_SCHEMA"] == $_SESSION['db_name']) {
                //echo var_dump($currentRecord);
                echo "<td>" . $currentRecord["COLUMN_NAME"] . "</td><td>";
                $maxLenght = preg_replace("/[^0-9]/", "", $currentRecord["COLUMN_TYPE"]);
                $dataType = strtok($currentRecord["COLUMN_TYPE"], '(');
                switch ($dataType) {
                    case "int":
                        echo "<input type=\"text\" pattern=\"\d*\" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" placeholder=\"numero di max " . $maxLenght . " cifre\"></input>";
                        break;
                    case "varchar":
                        echo $maxLenght > 20 ? "<textarea " : "<input type=\"text\"".
                        " \" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\" value=\"\" placeholder=\"testo di max " . $maxLenght . " caratteri\"></input>";
                        break;
                    case "date":
                        echo "<input type=\"date\" name=\"" . $currentRecord["COLUMN_NAME"] . "\" id=\"" . $currentRecord["COLUMN_NAME"] . "\"></input>";
                        break;
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