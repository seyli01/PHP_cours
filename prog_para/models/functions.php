<?php 

require_once '../config/database.php';

function display_velo() {
    global $mysqli;
    $sql = "SELECT * FROM velo";
    $result = $mysqli->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function add_velo($marque, $modele, $prix){
    global $mysqli;
    $sql = "INSERT INTO velo (marque, modele, prix) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $marque, $modele, $prix);
    return $stmt->execute();
}

// fonction pour supprimer ou modifier un vélo pour apres 



?>