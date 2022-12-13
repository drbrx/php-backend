<html>

<head>
    <!--<link rel="stylesheet" href="css/main.css">-->
    <link rel="stylesheet" href="../common/css/sidebar.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>MySQLInjection Control Panel</title>
</head>

<body>
    <div class="sidebar" style="width:10%; float: left">
        <div class="sidebarButtonCurrent"><a href="main.php">View</a></div>
        <div class="sidebarButton"><a href="../insert/insert.php">Insert</a></div>
    </div>

    <div style="float: left; width: 90%; background-color: #272a2e">
        <div id="searchBarWrapper" class="d-flex align-items-center" style="height: 50%;">
            <div id="searchBar" class="container">
                <form action="main.php" class="input-group input-group-lg">
                    <input type="text" placeholder="Search globally across all fields and records" name="search" class="form-control">
                    <button type="submit" class="btn btn-outline-primary" type="button">Go</button>
                </form>
            </div>
        </div>
        <div id="list" class="d-flex align-items-center flex-column justify-content-start" style="height: 50%;">
            <div id="tableWrapper" class="container-md">
                <?php
                require_once("../common/php/DBConnector.php");

                $connMySQL = new ConnectionMySQL();
                $pdo = $connMySQL->getConnection();
                $table = $_SESSION['table_name'];
                $maxReads = $_SESSION['rowsPerPage'];
                $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
                //echo "SELECT * FROM $table " . (isset($_REQUEST['search']) ? "WHERE" . generateFilter($_REQUEST['search'], $table, $configInfo) : "") . "LIMIT $maxReads OFFSET " . ($page != 0 ? (($page * $maxReads) - 1) : 0);
                $stmt = $pdo->prepare("SELECT * FROM $table " . (isset($_REQUEST['search']) ? "WHERE" . generateFilter($_REQUEST['search'], $table, $configInfo) : "") . "LIMIT $maxReads OFFSET " . ($page != 0 ? (($page * $maxReads) - 1) : 0));
                $stmt->execute();
                $stmtResponse = $stmt->fetchAll();
                $stmt = $pdo->prepare("SELECT * FROM $table " . (isset($_REQUEST['search']) ? "WHERE" . generateFilter($_REQUEST['search'], $table, $configInfo) : ""));
                $stmt->execute();
                $maxPages = ceil(count($stmt->fetchAll()) / $maxReads);
                if ($maxPages > 0) {
                    echo "  <table class=\"table table-dark table-hover\">
                        <thead>
                        <tr class=\"table-primary\"><th scope=\"col\">id</th>     
                        <th scope=\"col\">" . $configInfo[$table . 'MAINFIELD'] . "</th>
                        <th scope=\"col\"></th>
                    </tr> </thead>";
                    foreach ($stmtResponse as $currentRecord) {
                        echo    "<tr>   <td><p class=\"fw-bold\">" . $currentRecord['id'] . "</p></td>
                    <td>" . $currentRecord[$configInfo[$table . 'MAINFIELD']] . "</td>
                    <td><a class=\"btn btn-primary\" href=\"../details/details.php?id=" . $currentRecord['id'] . "\" role=\"button\">Details</a></td>
                    </tr>";
                    }
                    echo "</table>
                          </div>";
                } else {
                    echo "Non sono stati trovati dati. Prova con un filtro diverso se ne stai usando uno.";
                }

                ?>

                <div id="pageWrapper">
                    <a class="btn btn-dark" role="button" href="main.php?<?php echo isset($_REQUEST['search']) ? "search=" . $_REQUEST['search'] . "&" : "" ?>page=<?php echo ($page - 1) ?>" id="previousPage" class="pageButton" style="visibility:  <?php echo ($page <= 0 ? 'hidden;' : 'visible;') ?>">Previous</a>
                    <a class="btn btn-dark" role="button" href="main.php?<?php echo isset($_REQUEST['search']) ? "search=" . $_REQUEST['search'] . "&" : "" ?>page=<?php echo ($page + 1) ?>" id="nextPage" class="pageButton" style="visibility:  <?php echo ($page >= ($maxPages - 1) ? 'hidden;' : 'visible;') ?>">Next</a>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

<?php
function generateFilter($searchTerm, $tableName, $configInfo)
{
    $filter = "";

    $connMySQL = new ConnectionMySQL();
    $columnPdo = $connMySQL->getConnection();
    $comumnStmt = $columnPdo->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $tableName . "'");
    $comumnStmt->execute();
    $columnStmtResponse = $comumnStmt->fetchAll();

    foreach ($columnStmtResponse as $currentColumn) {
        if ($currentColumn["TABLE_SCHEMA"] == $_SESSION['db_name']) {
            if ($filter != "") {
                $filter .= "OR ";
            }
            if (isset($configInfo[$currentColumn["COLUMN_NAME"] . 'EXTERNAL']) && $configInfo[$currentColumn["COLUMN_NAME"] . 'EXTERNAL']) {

                $connMySQL = new ConnectionMySQL();
                $foreignPdo = $connMySQL->getConnection();
                $foreignStmt = $foreignPdo->prepare("SELECT id FROM " . strtolower(str_replace("id", '', $currentColumn["COLUMN_NAME"])) . " WHERE " . $configInfo['t' . strtolower(str_replace("id", '', $currentColumn["COLUMN_NAME"])) . 'MAINFIELD'] . " LIKE '%" . $searchTerm . "%'");
                $foreignStmt->execute();
                $foreignStmtResponse = $foreignStmt->fetchAll();
                if (isset($foreignStmtResponse[0]['id'])) {
                    $filter .= "`" . $currentColumn["COLUMN_NAME"] . "` LIKE '%" . $foreignStmtResponse[0]['id'] . "%' ";
                } else {
                    $filter .= "`" . $currentColumn["COLUMN_NAME"] . "` LIKE '%" . $searchTerm . "%' ";
                }
            } else {
                $filter .= "`" . $currentColumn["COLUMN_NAME"] . "` LIKE '%" . $searchTerm . "%' ";
            }
        }
    }

    return $filter;
}
