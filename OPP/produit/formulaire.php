<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire</title>
</head>
<body>
    <form action="traitement.php" method="post">

        <label for="id_product">ID Produit :</label>
        <input type="number" name="id_product" id="id_product" required>
        
        <label for="name">Nom du produit:</label>
        <input type="text" id="name" name="name"><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br><br>

        <label for="price">Prix:</label>
        <input type="number" id="price" name="price" step="0.01" min="0"
        <label for="stock_quantity">Quantité en stock:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" min="0"><br><br>

        <label for="category">Catégorie:</label>
        <input type="text" id="category" name="category"><br><br>

        <input type="submit" value="Ajouter le produit">
    </form>
</body>
</html>