<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Ajouter un contact</h1>
    <form action="create_contact.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" required>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" required>

        <label for="phone_number">Téléphone :</label>
        <input type="phone_number" name="phone_number" required>

        <label for="email">Email :</label>
        <input type="email" name="email" required>

        <button type="submit">Ajouter le contact</button>
    </form>
</body>
</html>