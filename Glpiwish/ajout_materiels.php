<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <form action="traitementFormMateriel.php" method="POST">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="type">Type:</label>
        <select id="type" name="type" required><br><br>
            <option value="">Sélectionnez un type</option>
            <option value="ordinateur">Ordinateur</option>
            <option value="imprimante">Imprimante</option>
            <option value="tablette">Tablette</option>
        </select><br><br>

        <label for="marque">Marque:</label>
        <input type="text" id="marque" name="marque" required><br><br>

        <label for="modele">Modèle:</label>
        <input type="text" id="modele" name="modele" required><br><br>

        <label for="date_achat">Date d'achat:</label>
        <input type="date" id="date_achat" name="date_achat" required><br><br>

        <label for="statut">Statut:</label>
        <select id="statut" name="statut" required>
            <option value="">Sélectionnez un statut</option>
            <option value="en stock">En stock</option>
            <option value="en panne">En panne</option>
            <option value="en utilisation">En utilisation</option>
        </select><br><br>

        <button type="submit">Ajouter le matériel</button>
    </form>
</body>

</html>