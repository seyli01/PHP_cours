<?php

require 'db.php';

$nom = 'Admin';
$prenom = 'GLPI';
$email = 'admin@glpi.com';
$password = 'admin';
$role = 'administrateur';

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nom, $prenom, $email, $hashed, $role);

if ($stmt->execute()) {
    echo "Administrateur ajouté avec succès dans la table utilisateurs.";
} else {
    echo "Erreur lors de l'ajout de l'administrateur : " . $mysqli->error;
}

?>