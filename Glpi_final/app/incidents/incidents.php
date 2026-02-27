<?php

require_once __DIR__ . '/../../utils.php';
require_once __DIR__ . '/../equipement/equipment.php';

function handle_add_incident($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
        $user_info = who_am_i();

        $id_utilisateur = $user_info['id'];
        $description = trim($_POST['description'] ?? '');
        $id_materiel = !empty($_POST['id_materiel']) ? (int) $_POST['id_materiel'] : null;
        $id_technicien_post = !empty($_POST['id_technicien']) ? (int) $_POST['id_technicien'] : null;

        if (empty($description) || !$id_materiel) {
            return "Veuillez remplir la description et sélectionner un matériel.";
        }

        $date = date('Y-m-d H:i:s');
        $status = 'ouvert';

        $id_technicien_final = $id_technicien_post;
        if (!$id_technicien_final) {
            $techs = get_techniciens($mysqli);
            if (!empty($techs)) {
                $random_tech = $techs[array_rand($techs)];
                $id_technicien_final = (int) $random_tech['id'];
            }
        }

        // Insérer l'incident
        $stmt = $mysqli->prepare("INSERT INTO incidents (description, date, status, id_utilisateur, id_technicien, id_materiel) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            return "Erreur de préparation SQL pour l'incident : " . $mysqli->error;
        }

        $stmt->bind_param("sssiii", $description, $date, $status, $id_utilisateur, $id_technicien_final, $id_materiel);

        if ($stmt->execute()) {
            $id_incident = $mysqli->insert_id;

            // Gestion de la pièce jointe

            if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['piece_jointe'];

                $upload_dir = __DIR__ . '/../../uploads/incidents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (in_array($file_extension, $allowed_extensions) && $file['size'] <= 5 * 1024 * 1024) {
                    // Nom fichier unique généré
                    $new_file_name = uniqid('incident_' . $id_incident . '_', true) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_file_name;
                    $db_path = 'uploads/incidents/' . $new_file_name;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $nom_original = $file['name'];

                        $stmt_pj = $mysqli->prepare("INSERT INTO pieces_jointes (nom_fichier, chemin_fichier, id_incident) VALUES (?, ?, ?)");
                        if ($stmt_pj) {
                            $stmt_pj->bind_param("ssi", $nom_original, $db_path, $id_incident);
                            $stmt_pj->execute();
                        }
                    }
                }
            }

            header("Location: index.php?success=1");
            exit;
        } else {
            return "Erreur lors de la création de l'incident : " . $stmt->error;
        }
    }

    return null;
}

// Fonction pour récupérer les incidents visibles par l'utilisateur connecté
function get_incidents_for_user($mysqli, $user_info)
{
    $role = $user_info['role'];
    $user_id = $user_info['id'];

    $sql = "SELECT i.*, 
                   m.nom as materiel_nom, m.marque as materiel_marque, m.modele as materiel_modele,
                   u_createur.nom as createur_nom, u_createur.prenom as createur_prenom,
                   u_tech.nom as tech_nom, u_tech.prenom as tech_prenom
            FROM incidents i
            LEFT JOIN materiels m ON i.id_materiel = m.id
            LEFT JOIN utilisateurs u_createur ON i.id_utilisateur = u_createur.id
            LEFT JOIN utilisateurs u_tech ON i.id_technicien = u_tech.id";

    // Filtrage automatique selon le rôle
    if ($role === 'administrateur') {
        $sql .= " ORDER BY i.date DESC";
        $stmt = $mysqli->prepare($sql);
    } elseif ($role === 'technicien') {
        $sql .= " WHERE i.id_technicien = ? ORDER BY i.date DESC";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
        }
    } else {
        // Utilisateur standard
        $sql .= " WHERE i.id_utilisateur = ? ORDER BY i.date DESC";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
        }
    }

    if (!$stmt) {
        return [];
    }

    $stmt->execute();
    $resultat = $stmt->get_result();

    $incidents = [];
    while ($row = $resultat->fetch_assoc()) {
        // Récupérer les pièces jointes
        $stmt_pj = $mysqli->prepare("SELECT nom_fichier, chemin_fichier FROM pieces_jointes WHERE id_incident = ?");
        if ($stmt_pj) {
            $stmt_pj->bind_param("i", $row['id']);
            $stmt_pj->execute();
            $pj_result = $stmt_pj->get_result();

            $row['pieces_jointes'] = [];
            while ($pj = $pj_result->fetch_assoc()) {
                $row['pieces_jointes'][] = $pj;
            }
        }
        $incidents[] = $row;
    }

    return $incidents;
}

// Fonction pour récupérer un incident par son ID
function get_incident_by_id($mysqli, $id_incident)
{
    $stmt = $mysqli->prepare("SELECT * FROM incidents WHERE id = ?");
    if (!$stmt)
        return null;

    $stmt->bind_param("i", $id_incident);
    $stmt->execute();
    $resultat = $stmt->get_result();

    return $resultat->fetch_assoc();
}

// Fonction pour modifier un incident
function handle_edit_incident($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editer') {
        $id_incident = (int) $_POST['id'];
        $status = $_POST['status'] ?? '';
        $id_technicien = !empty($_POST['id_technicien']) ? (int) $_POST['id_technicien'] : null;

        // On ne permet la modification que du statut et du technicien assigné pour garder ça simple
        if (empty($status)) {
            return "Statut invalide.";
        }

        $stmt = $mysqli->prepare("UPDATE incidents SET status = ?, id_technicien = ? WHERE id = ?");
        if (!$stmt) {
            return "Erreur SQL : " . $mysqli->error;
        }

        $stmt->bind_param("sii", $status, $id_technicien, $id_incident);

        if ($stmt->execute()) {
            header("Location: index.php?success_edit=1");
            exit;
        } else {
            return "Erreur lors de la modification : " . $stmt->error;
        }
    }
    return null;
}

// Fonction pour supprimer un incident
function handle_delete_incident($mysqli)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        $id_incident = (int) $_POST['id'];

        // Supprimer d'abord les fichiers physiques
        $stmt_pj = $mysqli->prepare("SELECT chemin_fichier FROM pieces_jointes WHERE id_incident = ?");
        if ($stmt_pj) {
            $stmt_pj->bind_param("i", $id_incident);
            $stmt_pj->execute();
            $result = $stmt_pj->get_result();
            while ($row = $result->fetch_assoc()) {
                $chemin_physique = __DIR__ . '/../../' . $row['chemin_fichier'];
                if (file_exists($chemin_physique)) {
                    unlink($chemin_physique);
                }
            }
        }

        $stmt = $mysqli->prepare("DELETE FROM incidents WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_incident);
            if ($stmt->execute()) {
                header("Location: index.php?success_delete=1");
                exit;
            } else {
                return "Erreur lors de la suppression : " . $stmt->error;
            }
        }
    }
    return null;
}
