<?php 

$host = 'localhost';
$user = 'root';
$password = 'root';
$db = 'gestion_velo';

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

?>