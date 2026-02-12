<?php

if($_SERVER["REQUEST_METHOD"] === "POST"){
    // RÉCUPÉRER LES DONNÉES DU FORMULAIRE

    print_r($_POST);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['phone_number'];

    // https://www.php.net/manual/fr/mysqli.prepare.php?id=example-4266
    // post method pour envoyer les données de manière sécurisé
require_once 'db.php';
// statement pour insérer les données dans la base de données
// requte non préparée (vulnérable aux injections SQL)
//$testSansPrepare = "INSERT INTO contacts (nom, prenom, telephone, email) 
//VALUES ('$nom', '$prenom', '$telephone', '$email')";

// le hacker il vas utiliser le champ de formulaire pour injecter du code SQL malveillant
// exemple : '); DROP TABLE contacts;--

// alors que une requête préparée vas protéger contre ça
// requête préparée (sécurisée contre les injections SQL) puisque on sépare le code SQL des données

$stmt = $conn->prepare("INSERT INTO contact (nom, prenom, telephone, email) values 
(?, ?, ?, ?)");

// ?? pour préciser l'ordre des types de données attendus ( emplacement réservé 
// pour les données)

// bind_param pour lier les variables aux emplacements réservés
// bind_value pour lier des valeurs spécifiques aux emplacements réservés
// bind value a prévligier quand on a des valeurs fixes
$stmt->bind_param("ssss", $nom, $prenom, $telephone, $email);
/*
$stmt->bind_param(":nom", $nom);
$stmt->bind_param(":prenom", $prenom);
$stmt->bind_param(":telephone", $telephone);
$stmt->bind_param(":email", $email);

*/



return $stmt->execute();

// exec ==> execute la requête et retourne le nombre de lignes affectées
// insertion / updat et delete



}




?>