<?php

require 'db.php';

$nom = 'Member';
$prenom = 'User';
$email = 'member@test.com';
$telephone = '0600000001';
$password = 'test';
$isAdmin = 0;

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO contact (nom, prenom, email, telephone, password, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $nom, $prenom, $email, $telephone, $hashed, $isAdmin);

if ($stmt->execute()) {
	echo "Member ajouté avec succès";
} else {
	echo "Erreur lors de l'ajout du member";
}

?>
