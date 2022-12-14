<head>
    <!--<link rel="stylesheet" href="css/details.css">-->
    <link rel="stylesheet" href="../common/css/sidebar.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>PHPYA Details</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<?php
require_once("../common/php/DBConnector.php");

$table = $_SESSION['table_name'];

$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id='" . $_REQUEST['id'] . "'");
$stmt->execute();
$stmtResponse = $stmt->fetchAll();
$currentRecord = $stmtResponse[0];

$connMySQLRows = new ConnectionMySQL();
$pdoRows = $connMySQLRows->getConnection();
$stmtRows = $pdoRows->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
$stmtRows->execute();
$stmtResponseRows = $stmtRows->fetchAll();
?>

<body>
    <div class="sidebar" style="width:10%; float: left">
        <div class="sidebarButton"><a href="../main/main.php">View</a></div>
        <div class="sidebarButton"><a href="../insert/insert.php">Insert</a></div>
        <div class="sidebarButtonCurrent"><a href="">Details</a></div>
    </div>

    <div style="float: left; width: 90%; background-color: #272a2e; height: 100%;" id="contents">
        <div id="form" class="d-flex align-items-center justify-content-center" style="height: 100%">

            <form action="php/save.php">
                <div id="tableWrapper" class="container-md">
                    <table class="table table-dark table-striped-columns">
                        <?php
                        foreach ($stmtResponseRows as $currentRecordRows) {

                            if ($currentRecordRows["TABLE_SCHEMA"] == $_SESSION['db_name']) {
                                if ($currentRecordRows["EXTRA"] != "auto_increment") {
                                    echo "<td class=\"fw-bold\">" . $currentRecordRows["COLUMN_NAME"] . "</td><td>";
                                    $maxLenght = preg_replace("/[^0-9]/", "", $currentRecordRows["COLUMN_TYPE"]);
                                    $dataType = strtok($currentRecordRows["COLUMN_TYPE"], '(');
                                    //echo $currentRecordRows["IS_NULLABLE"];

                                    switch (isset($configInfo[$currentRecordRows["COLUMN_NAME"]]) ? $configInfo[$currentRecordRows["COLUMN_NAME"]] : $dataType) {
                                        case "int":
                                            echo "<input class=\"form-control\" type=\"text\" pattern=\"\d*\" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\" placeholder=\"numero di max " . $maxLenght . " cifre\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . " " . "value=\"" . $currentRecord[$currentRecordRows['COLUMN_NAME']] . "\"></input>";
                                            break;
                                        case "varchar":
                                            echo ($maxLenght > 20 ? "<textarea class=\"form-control\"" : "<input class=\"form-control\" type=\"text\" value=\"" . $currentRecord[$currentRecordRows['COLUMN_NAME']] . "\"") .
                                                " \" maxlength=\"" . $maxLenght . "\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\" placeholder=\"testo di max " . $maxLenght . " caratteri\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . ">" . ($maxLenght > 20 ? $currentRecord[$currentRecordRows['COLUMN_NAME']] . "</textarea>" : "</input>");
                                            break;
                                        case "date":
                                            echo "<input type=\"date\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . " " . "value=\"" . $currentRecord[$currentRecordRows['COLUMN_NAME']] . "\"></input>";
                                            break;
                                        case "customText":
                                            echo "<input class=\"form-control\" type=\"text\" maxlength=\"" . $configInfo[$currentRecordRows["COLUMN_NAME"] . 'Length'] . "\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\" placeholder=\"stringa di max " . $configInfo[$currentRecordRows["COLUMN_NAME"] . 'Length'] . " caratteri\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . " " . "value=\"" . $currentRecord[$currentRecordRows['COLUMN_NAME']] . "\"></input>";
                                            break;
                                        case "checkbox":
                                            echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\"" . ($currentRecord[$currentRecordRows['COLUMN_NAME']] == 's' ? "checked" : "") . "></input>";
                                            break;
                                        case "select":
                                            $foreignTable = getForeignValues(strtolower(str_replace("id", '', $currentRecordRows["COLUMN_NAME"])), $configInfo);
                                            echo "<select class=\"form-select form-select-sm\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . "\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . ">";
                                            for ($i = 0; $i < count($foreignTable); $i++) {
                                                echo "<option value=\"" . $foreignTable[$i]['id'] . "\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . " " . ($currentRecord[$currentRecordRows['COLUMN_NAME']] == $i ? "selected=\"selected\"" : "") . ">" . $foreignTable[$i][$configInfo['t' . strtolower(str_replace("id", '', $currentRecordRows["COLUMN_NAME"])) . 'MAINFIELD']] .  "</option>";
                                            }
                                            echo "</select>";
                                            break;
                                        case "radio":
                                            $foreignTable = getForeignValues(strtolower(str_replace("id", '', $currentRecordRows["COLUMN_NAME"])), $configInfo);
                                            for ($i = 0; $i < count($foreignTable); $i++) {
                                                echo "<input class=\"form-check-input\" type=\"radio\" name=\"" . $currentRecordRows["COLUMN_NAME"] . "\" id=\"" . $currentRecordRows["COLUMN_NAME"] . $i . "\" value=\"" . $foreignTable[$i]['id'] . "\" " . ($currentRecordRows["IS_NULLABLE"] != "NO" ? "" : "required") . " " . ($currentRecord[$currentRecordRows['COLUMN_NAME']] == $i ? "checked=\"checked\"" : "") . ">" . $foreignTable[$i][$configInfo['t' . strtolower(str_replace("id", '', $currentRecordRows["COLUMN_NAME"])) . 'MAINFIELD']] . "</input>";
                                            }

                                            break;
                                    }
                                } else {
                                    echo "<td class=\"fw-bold\">" . $currentRecordRows["COLUMN_NAME"] . "</td>";
                                    echo "<td class=\"fw-bold\">" . $currentRecord[$currentRecordRows["COLUMN_NAME"]] . "</td>";
                                }
                                echo "</td></tr>";
                            }
                        }
                        ?>
                        <tr>
                            <td><input class="btn btn-success" type="submit" name="submit" value="save changes" /></td>
                            <td><input class="btn btn-warning" type="button" name="cancel" value="cancel changes" onclick="location.reload();" /></td>
                        </tr>
                        <tr>
                            <td><input class="btn btn-danger" type="button" name="delete" value="delete element" onclick="deleteElement(<?php echo $_REQUEST['id']; ?>)" /></td>
                            <td><input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>"></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

<script>
    function deleteElement(id) {
        if (confirm("Press 'OK' to PERMANENTLY delete this element (id " + id + ").") == true) {
            $.ajax({
                type: 'GET',
                url: 'php/delete.php',
                data: {
                    'id': id
                },
                success: function(result) {
                    $("#contents").html(result);
                    location.href = "php/deletionConfirmed.php?message=" + result;
                    return false;
                }
            })
        } else {
            alert("Element restored.");
        }
    }
</script>

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
