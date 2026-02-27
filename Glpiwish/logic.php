<?php

//contient toute la logique restante pour lappilcation

//Gestion utilisateur 

//check userrole

//Gestion des tickets (incidents)

function validerFormulaireMateriel($nom, $type, $marque, $modele, $date_achat, $statut)
{
	$erreurs = [];

	if (empty(trim($nom))) {
		$erreurs[] = "Le nom est requis.";
	}

	$typesValides = ["ordinateur", "imprimante", "tablette"];
	if (empty($type) || !in_array($type, $typesValides)) {
		$erreurs[] = "Le type sélectionné est invalide.";
	}

	if (empty(trim($marque))) {
		$erreurs[] = "La marque est requise.";
	}

	if (empty(trim($modele))) {
		$erreurs[] = "Le modèle est requis.";
	}

	if (empty($date_achat)) {
		$erreurs[] = "La date d'achat est requise.";
	}

	if (empty($statut)) {
		$erreurs[] = "Le statut est requis.";
	}

	return $erreurs;
}

function add_materiel($nom, $type, $marque, $modele, $date_achat, $statut, $id_employe = null)
{
	global $mysqli;
	$stmt = $mysqli->prepare("INSERT INTO materiels (nom, type, marque, modele, date_achat, status, id_employe) VALUES (?, ?, ?, ?, ?, ?, ?)");
	if (!$stmt) {
		return false;
	}
	$stmt->bind_param("ssssssi", $nom, $type, $marque, $modele, $date_achat, $statut, $id_employe);
	return $stmt->execute();
}

//remove materiel 

//update materiel 



?>
