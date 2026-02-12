<?php

//verifie la condition dans le session 
function isUserAdmin() {
    return isset($_SESSION['admin']['isAdmin']) && (int) $_SESSION['admin']['isAdmin'] === 1;
}

function add_students($nom, $prenom, $age, $classe) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO etudiants (nom, prenom, age, classe) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nom, $prenom, $age, $classe);
    return $stmt->execute();
}

function get_etudiants() {
    global $conn;
    $result = $conn->query("SELECT * FROM etudiants ORDER BY date_creation DESC");
    $etudiants = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $etudiants[] = $row;
        }
    }
    return $etudiants;
}

function get_etudiants_by_id($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM etudiants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function update_student($id, $nom, $prenom, $age, $classe) {
    global $conn;
    $stmt = $conn->prepare("UPDATE etudiants SET nom = ?, prenom = ?, age = ?, classe = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $nom, $prenom, $age, $classe, $id);
    return $stmt->execute();
}

function display_students() {
    $etudiants = get_etudiants();
    if (empty($etudiants)) {
        echo "Aucun étudiant enregistré";
        return;
    }
    foreach ($etudiants as $etudiant) {
        $id = (int) $etudiant['id'];
        $nom = $etudiant['nom'];
        $prenom = $etudiant['prenom'];
        $age = (int) $etudiant['age'];
        $classe = $etudiant['classe'];

        $actions = '';
        if (isUserAdmin()) {
            $actions = "<a class='btn-edit' href='?edit={$id}'>Modifier</a>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='action' value='supprimer'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn-delete'>Supprimer</button>
                </form>";
        }

        echo "<div class='etudiant-card'>
                <h3>{$prenom} {$nom}</h3>
                <p>Âge: {$age} ans</p>
                <p>Classe: {$classe}</p>
                {$actions}
              </div>";
    }
    return $etudiants;
}

function calculate_average_age() {
    global $conn;
    $result = $conn->query("SELECT AVG(age) as age_moyen FROM etudiants");
    if ($result && $row = $result->fetch_assoc()) {
        return round($row['age_moyen'] ?? 0);
    }
    return 0;
}

function count_by_class() {
    global $conn;
    $result = $conn->query("SELECT classe, COUNT(*) as nombre FROM etudiants GROUP BY classe");
    $repartition = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $repartition[$row['classe']] = $row['nombre'];
        }
    }
    return $repartition;
}

function count_students() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM etudiants");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'];
    }
    return 0;
}

function delete_student($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM etudiants WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function check_duplicates($nom, $prenom, $age) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM etudiants WHERE nom = ? AND prenom = ? AND age = ?");
    $stmt->bind_param("ssi", $nom, $prenom, $age); // ssi pour string, string et integer zehma
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function validerFormulaire($nom, $prenom, $age, $classe) {
    $erreurs = [];
    if (empty($nom) || !preg_match("/^[a-zA-Z'-]+$/", $nom)) {
        $erreurs[] = "Le nom est invalide.";
    }
    if (empty($prenom) || !preg_match("/^[a-zA-Z'-]+$/", $prenom)) {
        $erreurs[] = "Le prénom est invalide.";
    }
    if (!is_numeric($age) || $age < 15 || $age > 30) {
        $erreurs[] = "L'âge doit être un nombre entre 15 et 30.";
    }
    $classesValides = ["BTS SIO SISR", "BTS SIO SLAM"];
    if (!in_array($classe, $classesValides)) {
        $erreurs[] = "La classe sélectionnée est invalide.";
    }
    if (check_duplicates($nom, $prenom, $age)) {
        $erreurs[] = "Cet étudiant existe déjà dans la base de données.";
    }
    return $erreurs;
}

