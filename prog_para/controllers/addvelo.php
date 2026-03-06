<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    require_once '../models/functions.php';

    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $prix = $_POST['prix'];

    if(empty($marque) || empty($modele) || empty($prix)) {
        echo "Tous les champs sont requis.";
        exit();
    }

    $add_result = add_velo($marque, $modele, $prix);
    if($add_result) {
        echo "Vélo ajouté avec succès.";
        header("Location: ../index.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout du vélo.";
    }
}

?>