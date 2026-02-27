<?php

if(isset($_POST['email']) && isset($_POST['password'])) {
    require 'db.php';

    $email = $_POST['email'];
    $username = $_POST['email'];
    $password = $_POST['password'];

    // Utiliser un placeholder pour lier le paramètre proprement et éviter les injections SQL
    $demande = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $mysqli->prepare($demande);
    $stmt->bind_param("s",$email);
    $stmt->execute();
   $result = $stmt->get_result();
   if($result->num_rows > 0) {
       $user = $result->fetch_assoc();
       if(password_verify($password, $user['mot_de_passe'])) {
           session_start();
           $_SESSION['user_id'] = $user['id'];
           $_SESSION['user_email'] = $user['email'];
           $_SESSION['user_role'] = $user['role'];
           header("Location: index.php");
           exit();
       } else {
           echo "Mot de passe ou email incorrect.";
       }
   } else {
       echo "Mot de passe ou email incorrect.";
   }
}

?>