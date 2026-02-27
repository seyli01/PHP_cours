<?php

session_start();
if(!isset($_SESSION['user_id']) && !$_SESSION['role'] === 'administrateur'){
header("Location: index.php");
exit;
}
    // l'utilisateur est connecté et a le rôle d'administrateur, on affiche la page de gestion des matériels

    // l'utilisateur n'est pas connecté ou n'a pas le rôle d'administrateur, on le redirige vers la page d'accueil


// lister le matériels 
// on récupére les matériels de la base de données et on les affiche dans un tableau HTML
require 'db.php';
// 1 er étape ( squellette de la requete )
$demande = "SELECT * FROM materiels";
// 2 eme étape ( préparer la requete )
$stmt = $mysqli->prepare($demande);
// 3 eme étape ( lier les paramètres ) --- IGNORE ---

// 4 eme étape ( exécuter la requete )
$stmt->execute();
// 5 eme étape ( récupérer les résultats )
$result = $stmt->get_result();

// mysqli_fetch_all(FECTH_ASSOC)
$materiels = $result->fetch_all(MYSQLI_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
      <a href="ajout_materiels.php">Ajouter un matériel</a>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>type</th>
                <th>marque</th>
                <th>modele</th>
                <th>date_achat</th>
                <th>statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($materiels as $materiel): ?>
                <tr>
                    <td><?php echo $materiel['id']; ?></td>
                    <td><?php echo $materiel['type']; ?></td>
                    <td><?php echo $materiel['marque']; ?></td>
                    <td><?php echo $materiel['modele']; ?></td>
                    <td><?php echo $materiel['date_achat']; ?></td>
                    <td><?php echo $materiel['status']; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
               
            </tr>
    </table>

</body>

</html>

