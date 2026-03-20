<?php
session_start();
require_once 'produit.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$errors = [];
	$id_product = isset($_POST['id_product']) ? (int)$_POST['id_product'] : null;
	$name = trim($_POST['name'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$price = isset($_POST['price']) ? (float)$_POST['price'] : null;
	$stock_quantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : null;
	$category = trim($_POST['category'] ?? '');

	if (!$name) $errors[] = 'Nom obligatoire';
	if (!$description) $errors[] = 'Description obligatoire';
	if ($price === null || $price < 0) $errors[] = 'Prix invalide';
	if ($stock_quantity === null || $stock_quantity < 0) $errors[] = 'Stock invalide';
	if (!$category) $errors[] = 'Catégorie obligatoire';

	if (empty($errors)) {
		$product = new Product($id_product, $name, $description, $price, $stock_quantity, $category);
		$_SESSION['product'] = $product;
		echo '<h2>Données du produit enregistré :</h2>';
		echo 'ID: ' . $product->id_product . '<br>';
		echo 'Nom: ' . $product->name . '<br>';
		echo 'Description: ' . $product->description . '<br>';
		echo 'Prix: ' . $product->price . '<br>';
		echo 'Stock: ' . $product->stock_quantity . '<br>';
		echo 'Catégorie: ' . $product->category . '<br>';
	} else {
		foreach ($errors as $error) {
			echo '<p style="color:red">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
		}
	}
}
?>