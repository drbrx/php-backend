<?php
session_start();

$_SESSION['db_name'] = "appane";

$PDOdb_name = 'mysql:host=localhost;dbname=' . $_SESSION['db_name']. ';';
$PDOusername = "root";
$PDOpassword = "";
$_SESSION["db"] = new PDO($PDOdb_name, $PDOusername, $PDOpassword);

echo var_dump($_SESSION["db"]);
