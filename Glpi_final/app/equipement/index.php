<?php

require_once '../../utils.php';
require_once 'equipment.php';

require_admin();

$user_info = who_am_i();
$erreur_ajout = handle_add_equipment($mysqli);
$tous_les_utilisateurs = get_all_users($mysqli);
$equipements = get_all_equipments($mysqli);
$erreur_edit = handle_edit_equipment($mysqli);
$erreur_delete = handle_delete_equipment($mysqli);

$equipement_a_modifier = null;
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];
    $equipement_a_modifier = get_equipment_by_id($mysqli, $id_edit);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Matériels - GLPI Final</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../app.css">
</head>

<body class="app-body">

    <!-- Navbar inline -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fa-solid fa-desktop me-2"></i> GLPI Final
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Dashboard</a>
                    </li>
                    <?php if ($user_info && $user_info['isAdmin']): ?>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Gestion Matériels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../utilisateurs/index.php">Gestion Utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../incidents/index.php">Incidents</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? 'Utilisateur'); ?>
                    </span>
                    <a href="../../login/logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Gestion-->

    <div class="container mt-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1><i class="fa-solid fa-laptop me-2"></i> Gestion des Matériels</h1>
                <p class="text-muted">Gérez l'inventaire des équipements et périphériques.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                    <i class="fa-solid fa-plus me-1"></i> Ajouter un matériel
                </button>
            </div>
        </div>

        <!-- Affichage des alertes d'ajout (Succès ou Erreur) -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Le matériel a été ajouté avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($erreur_ajout): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($erreur_ajout); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Affichage des alertes de modification (Succès ou Erreur) -->
        <?php if (isset($_GET['success_edit'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> L'équipement a été modifié avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($erreur_edit): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($erreur_edit); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Affichage des alertes de suppression -->
        <?php if (isset($_GET['success_delete'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-trash-can me-2"></i> L'équipement a été supprimé avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($erreur_delete): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?php echo htmlspecialchars($erreur_delete); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Affichage de la grille de cartes -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

            <?php if (empty($equipements)): ?>
                <!-- Empty State (if no data) -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3"></i>
                            <p>Aucun matériel n'a été trouvé.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Boucle sur chaque équipement récupéré de la BDD -->
                <?php foreach ($equipements as $equipement): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- Barre de statut colorée en haut de la carte -->
                            <?php
                            $status_color = 'bg-secondary';
                            if ($equipement['status'] === 'en stock')
                                $status_color = 'bg-success';
                            if ($equipement['status'] === 'en utilisateur')
                                $status_color = 'bg-primary';
                            if ($equipement['status'] === 'en panne')
                                $status_color = 'bg-danger';
                            ?>
                            <div class="<?= $status_color ?>" style="height: 4px;"></div>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold text-dark mb-0">
                                        <?= htmlspecialchars($equipement['materiel_nom']) ?>
                                    </h5>
                                    <span class="badge <?= $status_color ?> rounded-pill">
                                        <?= htmlspecialchars(ucfirst($equipement['status'])) ?>
                                    </span>
                                </div>

                                <h6 class="card-subtitle mb-3 text-muted">
                                    <i class="fa-solid fa-tag me-1 text-secondary"></i>
                                    <?= htmlspecialchars(ucfirst($equipement['type'])) ?> -
                                    <?= htmlspecialchars($equipement['marque']) ?>
                                    (<?= htmlspecialchars($equipement['modele']) ?>)
                                </h6>

                                <div class="mb-3" style="font-size: 0.9rem;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fa-regular fa-calendar me-2 text-secondary" style="width: 16px;"></i>
                                        <span>Achat :
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($equipement['date_achat']))) ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-user me-2 text-secondary" style="width: 16px;"></i>
                                        <?php if ($equipement['employe_nom']): ?>
                                            <span>
                                                Assigné à :
                                                <strong><?= htmlspecialchars($equipement['employe_nom'] . ' ' . $equipement['employe_prenom']) ?></strong>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Non assigné</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action (Modifier/Supprimer) -->
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex gap-2">
                                    <a href="?edit=<?= $equipement['id'] ?>"
                                        class="btn btn-sm btn-outline-secondary flex-grow-1">
                                        <i class="fa-solid fa-pen"></i> Modifier
                                    </a>
                                    <form action="index.php" method="POST" class="m-0 p-0"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce matériel ? Cette action est irréversible.');">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $equipement['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    <!-- Modal d'Ajout de Matériel -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #111827;">
                    <h5 class="modal-title" id="addEquipmentModalLabel"><i class="fa-solid fa-plus me-2"></i>Nouveau
                        Matériel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addEquipmentForm" action="#" method="POST">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-medium text-secondary">Nom de l'équipement</label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                placeholder="Ex: PC Bureau Accueil" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label fw-medium text-secondary">Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="" selected disabled>Choisir un type...</option>
                                <option value="ordinateur">Ordinateur</option>
                                <option value="imprimante">Imprimante</option>
                                <option value="tablette">Tablette</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="marque" class="form-label fw-medium text-secondary">Marque</label>
                                <input type="text" class="form-control" id="marque" name="marque" placeholder="Ex: Dell"
                                    required>
                            </div>
                            <div class="col-6">
                                <label for="modele" class="form-label fw-medium text-secondary">Modèle</label>
                                <input type="text" class="form-control" id="modele" name="modele"
                                    placeholder="Ex: XPS 15" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="date_achat" class="form-label fw-medium text-secondary">Date d'achat</label>
                            <input type="date" class="form-control" id="date_achat" name="date_achat" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label fw-medium text-secondary">Statut initial</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="en stock" selected>En stock</option>
                                <option value="en utilisateur">En utilisateur</option>
                                <option value="en panne">En panne</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="id_employe" class="form-label fw-medium text-secondary">Assigner à
                                (Optionnel)</label>
                            <select class="form-select" id="id_employe" name="id_employe">
                                <option value="" selected>-- Ne pas assigner --</option>
                                <?php foreach ($tous_les_utilisateurs as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?>
                                        (<?= htmlspecialchars($u['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Laissez vide si le matériel est simplement mis "en stock".</div>
                        </div>
                        <!-- Placeholder form submission handled by frontend for now -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #111827; border: none;">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Modification de Matériel -->
    <?php if ($equipement_a_modifier): ?>
        <div class="modal fade show" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel"
            aria-hidden="true" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #111827;">
                        <h5 class="modal-title" id="editEquipmentModalLabel"><i class="fa-solid fa-pen me-2"></i>Modifier
                            Matériel</h5>
                        <a href="index.php" class="btn-close btn-close-white" aria-label="Fermer"></a>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editEquipmentForm" action="index.php" method="POST">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="id" value="<?= $equipement_a_modifier['id'] ?>">

                            <div class="mb-3">
                                <label for="edit_nom" class="form-label fw-medium text-secondary">Nom de
                                    l'équipement</label>
                                <input type="text" class="form-control" id="edit_nom" name="nom"
                                    value="<?= htmlspecialchars($equipement_a_modifier['nom']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_type" class="form-label fw-medium text-secondary">Type</label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="ordinateur" <?= $equipement_a_modifier['type'] === 'ordinateur' ? 'selected' : '' ?>>Ordinateur</option>
                                    <option value="imprimante" <?= $equipement_a_modifier['type'] === 'imprimante' ? 'selected' : '' ?>>Imprimante</option>
                                    <option value="tablette" <?= $equipement_a_modifier['type'] === 'tablette' ? 'selected' : '' ?>>Tablette</option>
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="edit_marque" class="form-label fw-medium text-secondary">Marque</label>
                                    <input type="text" class="form-control" id="edit_marque" name="marque"
                                        value="<?= htmlspecialchars($equipement_a_modifier['marque']) ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="edit_modele" class="form-label fw-medium text-secondary">Modèle</label>
                                    <input type="text" class="form-control" id="edit_modele" name="modele"
                                        value="<?= htmlspecialchars($equipement_a_modifier['modele']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_date_achat" class="form-label fw-medium text-secondary">Date
                                    d'achat</label>
                                <input type="date" class="form-control" id="edit_date_achat" name="date_achat"
                                    value="<?= htmlspecialchars($equipement_a_modifier['date_achat']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label fw-medium text-secondary">Statut</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="en stock" <?= $equipement_a_modifier['status'] === 'en stock' ? 'selected' : '' ?>>En stock</option>
                                    <option value="en utilisateur" <?= $equipement_a_modifier['status'] === 'en utilisateur' ? 'selected' : '' ?>>En utilisateur</option>
                                    <option value="en panne" <?= $equipement_a_modifier['status'] === 'en panne' ? 'selected' : '' ?>>En panne</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="edit_id_employe" class="form-label fw-medium text-secondary">Assigné à</label>
                                <select class="form-select" id="edit_id_employe" name="id_employe">
                                    <option value="">-- Ne pas assigner --</option>
                                    <?php foreach ($tous_les_utilisateurs as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= $equipement_a_modifier['id_employe'] == $u['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?>
                                            (<?= htmlspecialchars($u['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="index.php" class="btn btn-light border">Annuler</a>
                                <button type="submit" class="btn btn-primary"
                                    style="background-color: #111827; border: none;">Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>