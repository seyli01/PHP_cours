<?php
session_start();
require_once 'db.php';
require_once 'logic.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'] ?? '';
    $type = $_POST['type'] ?? '';
    $marque = $_POST['marque'] ?? '';
    $modele = $_POST['modele'] ?? '';
    $date_achat = $_POST['date_achat'] ?? '';
    $statut = $_POST['statut'] ?? '';

    $id_employe = $_SESSION['user_id'] ?? null; // Utilise la même clé de session que le login

    $erreurs = validerFormulaireMateriel($nom, $type, $marque, $modele, $date_achat, $statut);

    if (empty($erreurs)) {
        if (add_materiel($nom, $type, $marque, $modele, $date_achat, $statut, $id_employe)) {
            $message = "<p style='color: green;'>Le matériel a été ajouté avec succès.</p>";
        } else {
            $message = "<p style='color: red;'>Erreur lors de l'ajout du matériel.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Des erreurs sont survenues : <br>" . implode("<br>", $erreurs) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitement Matériel</title>
</head>

<body>
    <?= $message ?>
    <br><br>
    <a href="ajout_materiels.php">Retour au formulaire</a>
</body>

</html>