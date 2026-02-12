<?php

require 'db.php';

$nom = 'Admin';
$prenom = 'Root';
$email = 'admin@test.com';
$telephone = '0600000000';
$password = 'test';

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO contact (nom, prenom, email, telephone, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $hashed);

if ($stmt->execute()) {
	echo "Admin ajouté avec succès";
} else {
	echo "Erreur lors de l'ajout de l'admin";
}

?>