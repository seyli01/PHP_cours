<?php

require_once '../../utils.php';
require_once '../equipement/equipment.php';
require_once 'incidents.php';

safe_guarded_route();

$user_info = who_am_i();
$mes_equipements = get_equipements_by_user($mysqli, $user_info['id']);
$techniciens = get_techniciens($mysqli);

$erreur_ajout = handle_add_incident($mysqli);
$erreur_edit = handle_edit_incident($mysqli);
$erreur_delete = handle_delete_incident($mysqli);
$incidents = get_incidents_for_user($mysqli, $user_info);

$incident_a_modifier = null;
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];
    $incident_a_modifier = get_incident_by_id($mysqli, $id_edit);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Incidents - GLPI Final</title>
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
                            <a class="nav-link" href="../equipement/index.php">Gestion Matériels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../utilisateurs/index.php">Gestion Utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Incidents</a>
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
                <h1><i class="fa-solid fa-ticket me-2"></i> Gestion des Incidents</h1>
                <p class="text-muted">Suivi et résolution des tickets d'assistance.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIncidentModal">
                    <i class="fa-solid fa-plus me-1"></i> Nouveau Ticket
                </button>
            </div>
        </div>

        <!-- Affichage des alertes d'ajout -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Le ticket a été créé avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success_edit'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Le ticket a été modifié avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success_delete'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-trash-can me-2"></i> Le ticket a été supprimé avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($erreur_ajout) || !empty($erreur_edit) || !empty($erreur_delete)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?= htmlspecialchars($erreur_ajout ?? $erreur_edit ?? $erreur_delete) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tableau des incidents -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-secondary fw-semibold">ID</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Créateur</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Technicien assigné</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Matériel</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Description</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Date</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Pièces jointes</th>
                                <th class="px-4 py-3 text-secondary fw-semibold">Statut</th>
                                <th class="px-4 py-3 text-secondary fw-semibold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($incidents)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2"></i>
                                        <p class="mb-0">Aucun ticket trouvé.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($incidents as $inc): ?>
                                    <tr>
                                        <td class="px-4 fw-medium text-dark">#<?= $inc['id'] ?></td>
                                        <td class="px-4">
                                            <?= htmlspecialchars($inc['createur_prenom'] . ' ' . $inc['createur_nom']) ?>
                                        </td>
                                        <td class="px-4">
                                            <?php if ($inc['id_technicien']): ?>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fa-solid fa-hard-hat me-1 text-primary"></i>
                                                    <?= htmlspecialchars($inc['tech_prenom'] . ' ' . $inc['tech_nom']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Non assigné</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4">
                                            <?php if ($inc['id_materiel']): ?>
                                                <span class="text-secondary fw-medium">
                                                    <?= htmlspecialchars($inc['materiel_marque'] . ' ' . $inc['materiel_modele']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 text-truncate" style="max-width: 200px;"
                                            title="<?= htmlspecialchars($inc['description']) ?>">
                                            <?= htmlspecialchars($inc['description']) ?>
                                        </td>
                                        <td class="px-4 text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($inc['date'])) ?>
                                        </td>
                                        <td class="px-4">
                                            <?php if (!empty($inc['pieces_jointes'])): ?>
                                                <?php foreach ($inc['pieces_jointes'] as $pj): ?>
                                                    <a href="../../<?= htmlspecialchars($pj['chemin_fichier']) ?>" target="_blank"
                                                        class="badge bg-secondary text-decoration-none me-1"
                                                        title="<?= htmlspecialchars($pj['nom_fichier']) ?>">
                                                        <i class="fa-solid fa-paperclip"></i>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4">
                                            <?php
                                            $badge_class = 'bg-secondary';
                                            if ($inc['status'] === 'ouvert')
                                                $badge_class = 'bg-warning text-dark';
                                            if ($inc['status'] === 'en cours')
                                                $badge_class = 'bg-primary';
                                            if ($inc['status'] === 'fermé')
                                                $badge_class = 'bg-success';
                                            ?>
                                            <span class="badge <?= $badge_class ?> rounded-pill">
                                                <?= ucfirst($inc['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <?php if ($user_info['role'] === 'administrateur' || $user_info['role'] === 'technicien'): ?>
                                                    <a href="index.php?edit=<?= $inc['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($user_info['role'] === 'administrateur'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteIncidentModal<?= $inc['id'] ?>">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal de suppression pour cet incident -->
                                    <div class="modal fade" id="deleteIncidentModal<?= $inc['id'] ?>" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white border-bottom-0">
                                                    <h5 class="modal-title fw-bold"><i
                                                            class="fa-solid fa-triangle-exclamation me-2"></i>Confirmer la
                                                        suppression</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center">
                                                    <p class="mb-1">Êtes-vous sûr de vouloir supprimer définitivement le ticket
                                                        <strong>#<?= $inc['id'] ?></strong> ?
                                                    </p>
                                                    <p class="text-muted small">Ceci supprimera également toutes les pièces
                                                        jointes associées d'une façon irréversible.</p>
                                                    <form action="index.php" method="POST"
                                                        class="mt-4 d-flex justify-content-center gap-2">
                                                        <input type="hidden" name="action" value="supprimer">
                                                        <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                                        <button type="button" class="btn btn-light border"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification unique (Triggered par GET) -->
    <?php if ($incident_a_modifier): ?>
        <div class="modal fade show" id="editIncidentModal" tabindex="-1" aria-hidden="false"
            style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold">Modifier l'incident #<?= $incident_a_modifier['id'] ?></h5>
                        <a href="index.php" class="btn-close" aria-label="Close"></a>
                    </div>
                    <div class="modal-body p-4">
                        <form action="index.php" method="POST">
                            <input type="hidden" name="action" value="editer">
                            <input type="hidden" name="id" value="<?= $incident_a_modifier['id'] ?>">

                            <!-- Statut -->
                            <div class="mb-4">
                                <label class="form-label fw-medium text-secondary">Statut du ticket</label>
                                <select class="form-select" name="status" required>
                                    <option value="ouvert" <?= $incident_a_modifier['status'] === 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                                    <option value="en cours" <?= $incident_a_modifier['status'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="fermé" <?= $incident_a_modifier['status'] === 'fermé' ? 'selected' : '' ?>>
                                        Fermé</option>
                                </select>
                            </div>

                            <!-- Assignation (Admins uniquement) -->
                            <?php if (isset($user_info['role']) && $user_info['role'] === 'administrateur'): ?>
                                <div class="mb-4">
                                    <label class="form-label fw-medium text-secondary">Assigner à un technicien</label>
                                    <select class="form-select" name="id_technicien">
                                        <option value="" <?= is_null($incident_a_modifier['id_technicien']) ? 'selected' : '' ?>>
                                            Non assigné</option>
                                        <?php foreach ($techniciens as $tech): ?>
                                            <option value="<?= $tech['id'] ?>"
                                                <?= ($incident_a_modifier['id_technicien'] == $tech['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tech['prenom'] . ' ' . $tech['nom'] . ' (' . ucfirst($tech['email']) . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <!-- Si c'est un tech, on garde l'assignation actuelle cachée (ou inchangée côté server) -->
                                <input type="hidden" name="id_technicien"
                                    value="<?= htmlspecialchars($incident_a_modifier['id_technicien'] ?? '') ?>">
                            <?php endif; ?>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="index.php" class="btn btn-light border">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Modal d'ajout d'un incident -->
    <div class="modal fade" id="addIncidentModal" tabindex="-1" aria-labelledby="addIncidentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold" id="addIncidentModalLabel">
                        <i class="fa-solid fa-ticket text-primary me-2"></i>Nouveau Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="ajouter">

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium text-secondary">Description
                                détaillée</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required
                                placeholder="Décrivez le problème rencontré avec le matériel..."></textarea>
                        </div>

                        <!-- Matériel concerné -->
                        <div class="mb-4">
                            <label for="id_materiel" class="form-label fw-medium text-secondary">Matériel
                                concerné</label>
                            <select class="form-select" id="id_materiel" name="id_materiel" required>
                                <option value="" selected disabled>Sélectionnez un de vos équipements</option>
                                <?php foreach ($mes_equipements as $eq): ?>
                                    <option value="<?= $eq['id'] ?>">
                                        <?= htmlspecialchars($eq['marque'] . ' ' . $eq['modele'] . ' (' . $eq['nom'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Technicien assigné (Uniquement pour Admin) -->
                        <?php if (isset($user_info['role']) && $user_info['role'] === 'administrateur'): ?>
                            <div class="mb-4">
                                <label for="id_technicien" class="form-label fw-medium text-secondary">Assigner à
                                    (Optionnel)</label>
                                <select class="form-select" id="id_technicien" name="id_technicien">
                                    <option value="" selected>Non assigné</option>
                                    <?php foreach ($techniciens as $tech): ?>
                                        <option value="<?= $tech['id'] ?>">
                                            <?= htmlspecialchars($tech['prenom'] . ' ' . $tech['nom'] . ' (' . ucfirst($tech['email']) . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Si non renseigné, le ticket sera visible par tous les techniciens qui
                                    pourront se l'assigner.</div>
                            </div>
                        <?php endif; ?>

                        <!-- Pièce jointe -->
                        <div class="mb-4">
                            <label for="piece_jointe" class="form-label fw-medium text-secondary">Pièce jointe
                                (Optionnel)</label>
                            <input class="form-control" type="file" id="piece_jointe" name="piece_jointe">
                            <div class="form-text">Formats acceptés : JPG, PNG, PDF. Max 5Mo.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #111827; border: none;">Créer le ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>