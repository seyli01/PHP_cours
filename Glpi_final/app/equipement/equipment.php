<?php

require_once '../../db.php';
require_once '../../utils.php';

//fonction pour ajouter
function handle_add_equipment($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'ajouter') {
        return null;
    }

    //en soit la verif est pas utile vu que bah laccess cest que pour les admins 

    $user = who_am_i();
    if (!$user || !$user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $nom = trim($_POST['nom']);
    $type = $_POST['type'];
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $date_achat = $_POST['date_achat'];
    $statut = $_POST['status'];

    $id_employe = empty($_POST['id_employe']) ? $user['id'] : (int) $_POST['id_employe'];

    $stmt = $mysqli->prepare("INSERT INTO materiels (nom, type, marque, modele, date_achat, status, id_employe) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        return "Erreur serveur.";
    }

    $stmt->bind_param("ssssssi", $nom, $type, $marque, $modele, $date_achat, $statut, $id_employe);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit;
    } else {
        return "Erreur : " . $stmt->error;
    }
}

function get_all_users($mysqli)
{
    // On sélectionne les infos utiles des utilisateurs, triés par nom de famille
    $stmt = $mysqli->prepare("SELECT id, nom, prenom, email FROM utilisateurs ORDER BY nom ASC, prenom ASC");

    if (!$stmt) {
        return []; // En cas d'erreur de la BDD, on renvoie un tableau vide
    }

    $stmt->execute();
    $resultat = $stmt->get_result();

    $utilisateurs = [];
    while ($row = $resultat->fetch_assoc()) {
        $utilisateurs[] = $row;
    }

    return $utilisateurs;
}

function get_all_equipments($mysqli)
{
    $query = "
        SELECT m.id, m.nom as materiel_nom, m.type, m.marque, m.modele, m.date_achat, m.status, 
               u.nom AS employe_nom, u.prenom AS employe_prenom 
        FROM materiels m
        JOIN utilisateurs u ON m.id_employe = u.id
        ORDER BY m.id DESC
    ";

    $resultat = $mysqli->query($query);

    if (!$resultat) {
        return [];
    }

    $equipements = [];
    while ($row = $resultat->fetch_assoc()) {
        $equipements[] = $row;
    }

    return $equipements;
}

function get_equipment_by_id($mysqli, $id)
{
    $stmt = $mysqli->prepare("SELECT * FROM materiels WHERE id = ?");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultat = $stmt->get_result();

    return $resultat->fetch_assoc();
}

// Fonction pour traiter la soumission du formulaire de modification
function handle_edit_equipment($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'modifier') {
        return null;
    }

    $user = who_am_i();
    if (!$user || !$user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $id = (int) $_POST['id'];
    $nom = trim($_POST['nom']);
    $type = $_POST['type'];
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $date_achat = $_POST['date_achat'];
    $statut = $_POST['status'];

    $id_employe = empty($_POST['id_employe']) ? $user['id'] : (int) $_POST['id_employe'];

    $stmt = $mysqli->prepare("UPDATE materiels SET nom = ?, type = ?, marque = ?, modele = ?, date_achat = ?, status = ?, id_employe = ? WHERE id = ?");

    if (!$stmt) {
        return "Erreur serveur lors de la préparation de la requête.";
    }

    $stmt->bind_param("ssssssii", $nom, $type, $marque, $modele, $date_achat, $statut, $id_employe, $id);

    if ($stmt->execute()) {
        header("Location: index.php?success_edit=1");
        exit;
    } else {
        return "Erreur lors de la modification : " . $stmt->error;
    }
}

// Fonction pour traiter la suppression d'un équipement
function handle_delete_equipment($mysqli)
{
    // On vérifie que c'est une requête POST et que l'action est bien "supprimer"
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'supprimer') {
        return null;
    }

    $user = who_am_i();
    if (!$user || !$user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $id = (int) $_POST['id'];

    $stmt = $mysqli->prepare("DELETE FROM materiels WHERE id = ?");

    if (!$stmt) {
        return "Erreur serveur lors de la préparation de la requête de suppression.";
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?success_delete=1");
        exit;
    } else {
        return "Erreur lors de la suppression : " . $stmt->error;
    }
}

// Fonction pour récupérer le nombre d'équipements assignés à un utilisateur spécifique
function get_equipment_count_by_user($mysqli, $id_employe)
{
    $stmt = $mysqli->prepare("SELECT COUNT(*) as nb_equipements FROM materiels WHERE id_employe = ?");

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("i", $id_employe);
    $stmt->execute();
    $resultat = $stmt->get_result();

    if ($row = $resultat->fetch_assoc()) {
        return (int) $row['nb_equipements'];
    }

    return 0;
}

// Fonction pour récupérer la liste de tous les équipements assignés à un utilisateur spécifique
function get_equipements_by_user($mysqli, $id_employe)
{
    $stmt = $mysqli->prepare("SELECT * FROM materiels WHERE id_employe = ? ORDER BY nom ASC");

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $id_employe);
    $stmt->execute();
    $resultat = $stmt->get_result();

    $equipements = [];
    while ($row = $resultat->fetch_assoc()) {
        $equipements[] = $row;
    }

    return $equipements;
}

// Fonction pour récupérer la liste des techniciens et administrateurs
function get_techniciens($mysqli)
{
    $stmt = $mysqli->prepare("SELECT id, nom, prenom, email FROM utilisateurs WHERE role IN ('technicien', 'administrateur') ORDER BY nom ASC");

    if (!$stmt) {
        return [];
    }

    $stmt->execute();
    $resultat = $stmt->get_result();

    $techs = [];
    while ($row = $resultat->fetch_assoc()) {
        $techs[] = $row;
    }

    return $techs;
}
