<a href="../insert/insert.php">Insert</a><br></br>

<?php
require_once("../common/php/DBConnector.php");

$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = $_SESSION['table_name'];
$maxReads = $_SESSION['rowsPerPage'];
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$stmt = $pdo->prepare("SELECT * FROM $table LIMIT $maxReads OFFSET " . ($page != 0 ? (($page * $maxReads) - 1) : 0));
$stmt->execute();
$stmtResponse = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM $table WHERE 1");
$stmt->execute();
$maxPages = ceil(count($stmt->fetchAll()) / $maxReads);

echo '  <table>
        <tr><th>Id</th>     <th>Testo</th>
        </tr>';
foreach ($stmtResponse as $currentRecord) {
        echo    '<tr>   <td>' . $currentRecord['id'] . "</td>
                    <td>" . $currentRecord['testo'] . "</td>
                    <td><a href=\"#\">Modfy</a></td>
                    <td><a href=\"#\">Delete</a></td>
            </tr>";
}
echo '</table>';
?>

<div id="pageWrapper">
        <a href="main.php?page=<?php echo ($page - 1) ?>" id="previousPage" style="visibility:  <?php echo ($page <= 0 ? 'hidden;' : 'visible;') ?>">Previous</a>
        <a href="main.php?page=<?php echo ($page + 1) ?>" id="nextPage" style="visibility:  <?php echo ($page >= ($maxPages - 1) ? 'hidden;' : 'visible;') ?>">Next</a>
</div>