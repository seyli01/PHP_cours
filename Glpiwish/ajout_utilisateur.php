<?php

session_start();
if(!isset($_SESSION['user_id']) && $_SESSION['user_role'] !== 'administrateur') {
    header("Location: index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db.php';

    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $demande = "INSERT INTO utilisateurs (prenom, email, mot_de_passe, role) VALUES ( ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($demande);
    $stmt->bind_param("sss", $email, $hashed_password, $role);
    
    if($stmt->execute()) {
        echo "Utilisateur ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'utilisateur : " . $stmt->error;
    }
}

?>