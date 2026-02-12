<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP PHP - Gestion des Étudiants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .etudiant-card {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 TP PHP - Gestion des Étudiants BTS SIO</h1>

<?php
// Démarrer la session pour stocker les données
session_start();

// Initialiser le tableau des étudiants s'il n'existe pas
if (!isset($_SESSION['etudiants'])) {
    $_SESSION['etudiants'] = [];
}

// ========== ZONE DE CODE - À COMPLÉTER PAR LES ÉTUDIANTS ==========

// TODO: Créer la fonction ajouterEtudiant($nom, $prenom, $age, $classe)
// Cette fonction doit créer un tableau associatif avec les infos de l'étudiant
// et l'ajouter à $_SESSION['etudiants']

function ajouterEtudiant($nom, $prenom, $age, $classe) {
    $etudiant = [
        'nom' => $nom,
        'prenom' => $prenom,
        'age' => $age,
        'classe' => $classe
    ];
    $_SESSION['etudiants'][] = $etudiant;
};


// TODO: Créer la fonction afficherEtudiants()
// Cette fonction doit afficher tous les étudiants dans des cards HTML

function afficherEtudiants() {
    if (empty($_SESSION['etudiants'])) {
        echo "Aucun étudiant enregistré";
        return;
    }
    foreach ($_SESSION['etudiants'] as $etudiant) {
        echo "<div class='etudiant-card'>
                <h3>{$etudiant['prenom']} {$etudiant['nom']}</h3>
                <p>Âge: {$etudiant['age']}</p>
                <p>Classe: {$etudiant['classe']}</p>
              </div>";
    }
};

// TODO: Créer la fonction calculerAgeMoyen()
// Cette fonction doit retourner l'âge moyen de tous les étudiants

function calculerAgeMoyen() {
    $totalAge = 0;
    $nombreEtudiants = count($_SESSION['etudiants']);
    if ($nombreEtudiants === 0) {
        return 0;
    }
    foreach ($_SESSION['etudiants'] as $etudiant) {
        $totalAge += $etudiant['age'];
    }
    return round($totalAge / $nombreEtudiants);
};


// TODO: Créer la fonction compterParClasse()
// Cette fonction doit retourner un tableau avec le nombre d'étudiants par classe

function compterParClasse() {
    $repartition = [];
    foreach ($_SESSION['etudiants'] as $etudiant) {
        $classe = $etudiant['classe'];
        if (!isset($repartition[$classe])) {
            $repartition[$classe] = 0;
        }
        $repartition[$classe]++;
    }
    return $repartition;
};

// TODO: Créer la fonction validerFormulaire($nom, $prenom, $age, $classe)
// Cette fonction doit vérifier que toutes les données sont correctes
// et retourner un tableau d'erreurs (vide si tout est OK)

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

    return $erreurs;
};

// ========== TRAITEMENT DU FORMULAIRE ==========

$message = "";

// TODO: Compléter le traitement du formulaire
// Vérifier si le formulaire a été soumis
// Récupérer les données POST
// Valider les données avec votre fonction
// Si OK: ajouter l'étudiant et afficher un message de succès
// Sinon: afficher les erreurs

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age = $_POST['age'];
    $classe = $_POST['classe'];
    
    $erreurs = validerFormulaire($nom, $prenom, $age, $classe);
    
    // Si pas d'erreurs
    if (empty($erreurs)) {
        ajouterEtudiant($nom, $prenom, $age, $classe);
        $message = "<div class='message success'> Étudiant ajouté avec succès !</div>";
    } else {
        // Afficher les erreurs
        $message = "<div class='message error'>";
        foreach ($erreurs as $erreur) {
            $message .= "hell nah" . $erreur . "<br>";
        }
        $message .= "</div>";
    }
}

echo $message;

?>

        <!-- FORMULAIRE D'AJOUT -->
        <h2>Ajouter un Étudiant</h2>
        <form method="POST" action="">
            
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
            
            <label for="age">Âge :</label>
            <input type="number" id="age" name="age" min="15" max="30" required>
            
            <label for="classe">Classe :</label>
            <select id="classe" name="classe" required>
                <option value="">-- Choisir --</option>
                <option value="BTS SIO SISR">BTS SIO SISR</option>
                <option value="BTS SIO SLAM">BTS SIO SLAM</option>
            </select>
            
            <button type="submit">Ajouter l'étudiant</button>
        </form>

        <hr>

        <!-- TODO: AFFICHAGE DES STATISTIQUES -->
        <!-- Utiliser vos fonctions pour afficher:
             - Le nombre total d'étudiants
             - L'âge moyen
             - La répartition par classe
        -->
             <!-- AFFICHAGE DES STATISTIQUES -->
        <h2>Statistiques</h2>

        <?php
        $nombreTotal = count($_SESSION['etudiants']);
        $ageMoyen = calculerAgeMoyen();
        $repartition = compterParClasse();
        ?>

        <div class="etudiant-card">
            <p><strong>Nombre total d'étudiants :</strong> <?php echo $nombreTotal; ?></p>
            <p><strong>Âge moyen :</strong> <?php echo $ageMoyen; ?> ans</p>
            <p><strong>Répartition par classe :</strong></p>
            <ul>
                <?php
                foreach ($repartition as $classe => $nombre) {
                    echo "<li>$classe : $nombre étudiant(s)</li>";
                }
                ?>
            </ul>
        </div>

        <hr>

        <!-- TODO: AFFICHAGE DES ÉTUDIANTS -->
        <!-- Appeler votre fonction afficherEtudiants() ici -->
        <h2>Liste des Étudiants</h2>
        <?php afficherEtudiants(); ?>
    </div>
</body>
</html>