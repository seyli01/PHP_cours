<?php

require_once 'models/functions.php';
$velos = display_velo();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afficher Vélos</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($velos as $velo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($velo['marque']); ?></td>
                    <td><?php echo htmlspecialchars($velo['modele']); ?></td>
                    <td><?php echo htmlspecialchars($velo['prix']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>