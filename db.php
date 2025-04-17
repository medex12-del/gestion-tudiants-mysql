<?php
$host = '127.0.0.1';
$username = 'root';
$password = '';
$dbname = 'gestionEtud';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Échec de la connexion à la base de données : " . mysqli_connect_error());
}
?>
