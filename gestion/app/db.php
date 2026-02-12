<?php
// Paramètres de connexion
$host = "localhost";
$username = "root";
$password = "root";
$database = "contacts";

// Connexion
$conn = new mysqli($host, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

echo "Connecté avec succès";
?>