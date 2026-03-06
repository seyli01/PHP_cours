<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un vélo</title>
</head>

<body>
    <h1>Ajouter un vélo</h1>
    <form action="../controller/add_velo.php" method="POST">
        <label for="marque">Marque:</label><br>
        <input type="text" id="marque" name="marque" required><br><br>

        <label for="modele">Modèle:</label><br>
        <input type="text" id="modele" name="modele" required><br><br>

        <label for="prix">Prix:</label><br>
        <input type="number" id="prix" name="prix" step="0.01" min="0" required><br><br>

        <input type="submit" value="Ajouter">
    </form>

    <a href="../index.php">retour</a>
    
</body>
</html>