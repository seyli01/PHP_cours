<?php

require_once '../../db.php';
require_once '../../utils.php';

// Fonction pour ajouter un utilisateur
function handle_add_user($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'ajouter') {
        return null;
    }

    $user = who_am_i();
    if (!$user || !$user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    // Vérifier si l'email existe déjà
    $stmt_check = $mysqli->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        return "Erreur : Cet email est déjà utilisé.";
    }

    // Hasher le mot de passe avant de l'enregistrer (obligatoire pour la sécurité !)
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        return "Erreur serveur lors de la préparation de la requête.";
    }

    $stmt->bind_param("sssss", $nom, $prenom, $email, $mot_de_passe_hash, $role);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit;
    } else {
        return "Erreur : " . $stmt->error;
    }
}

// Fonction pour récupérer tous les utilisateurs (réutilisable ou doublon de celle dans equipment, mais c'est mieux d'avoir son propre CRUD)
function get_all_users_list($mysqli)
{
    $query = "SELECT id, nom, prenom, email, role FROM utilisateurs ORDER BY nom ASC, prenom ASC";
    $resultat = $mysqli->query($query);

    if (!$resultat) {
        return [];
    }

    $utilisateurs = [];
    while ($row = $resultat->fetch_assoc()) {
        $utilisateurs[] = $row;
    }

    return $utilisateurs;
}

// Fonction pour récupérer un seul utilisateur par son ID (pour pré-remplir le formulaire)
function get_user_by_id($mysqli, $id)
{
    $stmt = $mysqli->prepare("SELECT id, nom, prenom, email, role FROM utilisateurs WHERE id = ?");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultat = $stmt->get_result();

    return $resultat->fetch_assoc();
}

// Fonction pour traiter la soumission du formulaire de modification
function handle_edit_user($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'modifier') {
        return null;
    }

    $current_user = who_am_i();
    if (!$current_user || !$current_user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $id = (int) $_POST['id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);

    // On récupère le rôle directement depuis le select (ou si c'est l'admin lui-même qui s'édite, c'est disabled donc on garde la valeur hidden 'administrateur')
    $role = $_POST['role'] ?? 'administrateur';

    // Vérifier si le nouvel email existe déjà (et que ce n'est pas le sien)
    $stmt_check = $mysqli->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        return "Erreur : Cet email est déjà utilisé par un autre utilisateur.";
    }

    // Si on a fourni un nouveau mot de passe, on le met à jour
    if (!empty($_POST['mot_de_passe'])) {
        $mot_de_passe_hash = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nom, $prenom, $email, $mot_de_passe_hash, $role, $id);
    } else {
        // Sinon, on met à jour uniquement le reste
        $stmt = $mysqli->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $role, $id);
    }

    if (!$stmt) {
        return "Erreur serveur lors de la préparation de la requête.";
    }

    if ($stmt->execute()) {
        header("Location: index.php?success_edit=1");
        exit;
    } else {
        return "Erreur lors de la modification : " . $stmt->error;
    }
}

// Fonction pour traiter la suppression d'un utilisateur
function handle_delete_user($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'supprimer') {
        return null;
    }

    $current_user = who_am_i();
    if (!$current_user || !$current_user['isAdmin']) {
        return "Erreur : Droits insuffisants.";
    }

    $id = (int) $_POST['id'];

    if ($id === $current_user['id']) {
        return "Erreur : Vous ne pouvez pas supprimer votre propre compte.";
    }

    $admin_id_pour_reassignation = 1;
    $stmt_update_materiel = $mysqli->prepare("UPDATE materiels SET id_employe = ? WHERE id_employe = ?");
    if ($stmt_update_materiel) {
        $stmt_update_materiel->bind_param("ii", $admin_id_pour_reassignation, $id);
        $stmt_update_materiel->execute();
    }

    $stmt = $mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");

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

// Fonction pour générer un mot de passe aléatoire robuste (PHP)
function generate_random_password($length = 12)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}