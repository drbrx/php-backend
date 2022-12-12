<head>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="../common/css/sidebar.css">
</head>

<body>
        <div class="sidebar" style="width:10%; float: left">
                <div class="sidebarButtonCurrent"><a href="main.php">View</a></div>
                <div class="sidebarButton"><a href="../insert/insert.php">Insert</a></div>
        </div>

        <div style="float: left; width: 90%">
                <div id="searchBarWrapper">
                        <div id="searchBar">
                                <form action="main.php">
                                        <input type="text" placeholder="Search..." name="search">
                                        <button type="submit">Go</button>
                                </form>
                        </div>
                </div>
                <div id="list">
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
                                echo '  <table>
        <tr><th>id</th>     <th>' . $configInfo[$table . 'MAINFIELD'] . '</th>
        </tr>';
                                foreach ($stmtResponse as $currentRecord) {
                                        echo    '<tr>   <td>' . $currentRecord['id'] . "</td>
                    <td>" . $currentRecord[$configInfo[$table . 'MAINFIELD']] . "</td>
                    <td><a href=\"../details/details.php?id=" . $currentRecord['id'] . "\">Details</a></td>
            </tr>";
                                }
                                echo '</table>';
                        } else {
                                echo "Non sono stati trovati dati. Prova con un filtro diverso se ne stai usando uno.";
                        }

                        ?>

                        <div id="pageWrapper">
                                <a href="main.php?<?php echo isset($_REQUEST['search']) ? "search=" . $_REQUEST['search'] . "&" : "" ?>page=<?php echo ($page - 1) ?>" id="previousPage" class="pageButton" style="visibility:  <?php echo ($page <= 0 ? 'hidden;' : 'visible;') ?>">Previous</a>
                                <a href="main.php?<?php echo isset($_REQUEST['search']) ? "search=" . $_REQUEST['search'] . "&" : "" ?>page=<?php echo ($page + 1) ?>" id="nextPage" class="pageButton" style="visibility:  <?php echo ($page >= ($maxPages - 1) ? 'hidden;' : 'visible;') ?>">Next</a>
                        </div>
                </div>
        </div>
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
