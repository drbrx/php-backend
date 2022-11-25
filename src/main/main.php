<a href="../insert/insert.php">Insert</a>

<?php
$connMySQL = new ConnectionMySQL();
$pdo = $connMySQL->getConnection();
$table = 'tabella';
$stmt = $pdo->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "'");
$stmt->execute();
$stmtResponse = $stmt->fetchAll();
