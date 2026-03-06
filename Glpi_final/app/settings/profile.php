<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../utils.php';

safe_guarded_route();

// Récupère le profil complet de l'utilisateur connecté depuis la BDD
function fetch_user_profile($mysqli)
{
    $user = who_am_i();
    if (!$user) {
        return null;
    }

    $stmt = $mysqli->prepare("SELECT id, nom, prenom, email, role, avatar FROM utilisateurs WHERE id = ?");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();

    //fallback avatar si yen a pas 
    if ($profile) {
        $profile['avatar_url'] = !empty($profile['avatar'])
            ? '/PHP_cours/Glpi_final/uploads/avatars/' . $profile['avatar']
            : '/PHP_cours/default-avatar-icon-of-social-media-user-vector.jpg';
    }

    return $profile;
}

// Gère l'upload d'un nouvel avatar
// Retourne le nom du fichier enregistré, une string d'erreur, ou null si pas d'upload
function handle_avatar_upload($mysqli)
{
    if (empty($_FILES['avatar']['name'])) {
        return null;
    }

    $user = who_am_i();
    if (!$user) {
        return "Erreur : utilisateur non connecté.";
    }

    $file      = $_FILES['avatar'];
    $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size  = 2 * 1024 * 1024; // 2 Mo

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Erreur lors de l'upload du fichier.";
    }
    if (!in_array($file['type'], $allowed)) {
        return "Format non autorisé. Utilisez JPG, PNG, GIF ou WEBP.";
    }
    if ($file['size'] > $max_size) {
        return "L'image ne doit pas dépasser 2 Mo.";
    }

    $ext          = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $user['id'] . '_' . time() . '.' . strtolower($ext);
    $dest         = __DIR__ . '/../../uploads/avatars/' . $new_filename;

    // Supprimer l'ancien avatar si existant
    $old = fetch_user_profile($mysqli);
    if (!empty($old['avatar'])) {
        $old_path = __DIR__ . '/../../uploads/avatars/' . $old['avatar'];
        if (file_exists($old_path)) {
            unlink($old_path);
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return "Impossible de sauvegarder l'image.";
    }

    // Mettre à jour en BDD
    $stmt = $mysqli->prepare("UPDATE utilisateurs SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $new_filename, $user['id']);
    $stmt->execute();

    return $new_filename;
}

function update_user_profile($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'modifier_profil') {
        return null;
    }

    $user = who_am_i();
    if (!$user) {
        return "Erreur : utilisateur non connecté.";
    }

    // Gérer l'avatar en premier
    $avatar_result = handle_avatar_upload($mysqli);
    if (is_string($avatar_result) && str_starts_with($avatar_result, 'Erreur')) {
        return $avatar_result;
    }

    $id     = $user['id'];
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email  = trim($_POST['email'] ?? '');

    if (empty($nom) || empty($prenom) || empty($email)) {
        return "Erreur : tous les champs obligatoires doivent être remplis.";
    }

    // Vérifier si l'email est déjà pris par quelqu'un d'autre
    $stmt_check = $mysqli->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        return "Erreur : cet email est déjà utilisé par un autre compte.";
    }

    // Mise à jour avec ou sans mot de passe
    if (!empty($_POST['mot_de_passe'])) {
        $hash = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $hash, $id);
    } else {
        $stmt = $mysqli->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nom, $prenom, $email, $id);
    }

    if (!$stmt) {
        return "Erreur serveur lors de la préparation de la requête.";
    }

    if ($stmt->execute()) {
        $_SESSION['user_email'] = $email;
        return true;
    }

    return "Erreur lors de la mise à jour : " . $stmt->error;
}


