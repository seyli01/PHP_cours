<?php

require_once '../../utils.php';
require_once 'users.php';
require_once '../equipement/equipment.php';

require_admin();

$user_info = who_am_i();
$erreur_ajout = handle_add_user($mysqli);
$utilisateurs = get_all_users_list($mysqli);
$erreur_edit = handle_edit_user($mysqli);
$erreur_delete = handle_delete_user($mysqli);

$utilisateur_a_modifier = null;
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];
    $utilisateur_a_modifier = get_user_by_id($mysqli, $id_edit);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - GLPI Final</title>
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
                            <a class="nav-link active" aria-current="page" href="index.php">Gestion Utilisateurs</a>
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
                <h1><i class="fa-solid fa-users me-2"></i> Gestion des Utilisateurs</h1>
                <p class="text-muted">Gérez les comptes d'accès à la plateforme et leurs droits.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fa-solid fa-user-plus me-1"></i> Ajouter un utilisateur
                </button>
            </div>
        </div>

        <!-- Alertes Ajout -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> L'utilisateur a été ajouté avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($erreur_ajout): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($erreur_ajout); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Alertes Modif -->
        <?php if (isset($_GET['success_edit'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> L'utilisateur a été mis à jour !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($erreur_edit): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($erreur_edit); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Alertes Suppression -->
        <?php if (isset($_GET['success_delete'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-trash-can me-2"></i> L'utilisateur a été supprimé ! Tous ses équipements sont
                désormais "Non assignés".
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($erreur_delete): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($erreur_delete); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <!-- Affichage de la grille de cartes -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <?php if (empty($utilisateurs)): ?>
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center text-muted py-5">
                            <i class="fa-solid fa-users-slash fa-3x mb-3"></i>
                            <p>Aucun utilisateur trouvé.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($utilisateurs as $user): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- Barre de statut colorée en haut de la carte : Admin = Rouge/Danger, Tech = Bleu/Info, User = Gris/Secondary -->
                            <?php
                            if ($user['role'] === 'administrateur') {
                                $status_color = 'bg-danger';
                            } elseif ($user['role'] === 'technicien') {
                                $status_color = 'bg-info';
                            } else {
                                $status_color = 'bg-secondary';
                            }
                            ?>
                            <div class="<?= $status_color ?>" style="height: 4px;"></div>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center">
                                        <!-- Affichage de l'avatar basé sur l'email -->
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-user text-secondary"></i>
                                        </div>
                                        <div>
                                            <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                                            <?php if ($user['id'] === $user_info['id']): ?>
                                                <span class="badge bg-primary ms-2" style="font-size: 0.7em;">(Vous)</span>
                                            <?php endif; ?>
                                        </div>
                                    </h5>

                                    <span class="badge <?= $status_color ?> rounded-pill">
                                        <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                    </span>
                                </div>

                                <h6 class="card-subtitle mb-2 text-muted mt-3" style="margin-left: 55px;">
                                    <i class="fa-solid fa-envelope me-1 text-secondary"></i>
                                    <?= htmlspecialchars($user['email']) ?>
                                </h6>

                                <?php $nb_equipements = get_equipment_count_by_user($mysqli, $user['id']); ?>
                                <p class="card-text text-muted mb-3" style="margin-left: 55px; font-size: 0.9em;">
                                    <i class="fa-solid fa-laptop text-secondary me-1"></i>
                                    <strong><?= $nb_equipements ?></strong> équipement(s) assigné(s)
                                </p>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex gap-2">
                                    <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary flex-grow-1">
                                        <i class="fa-solid fa-pen"></i> Modifier
                                    </a>

                                    <!-- Pour ne pas se supprimer soi-même depuis l'interface -->
                                    <?php if ($user['id'] !== $user_info['id']): ?>
                                        <form action="index.php" method="POST" class="m-0 p-0"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-danger disabled"
                                            title="Vous ne pouvez pas vous supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>


    <!-- Modal d'Ajout Utilisateur -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #111827;">
                    <h5 class="modal-title" id="addUserModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Nouveau
                        Compte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addUserForm" action="index.php" method="POST">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="nom" class="form-label fw-medium text-secondary">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="col-6">
                                <label for="prenom" class="form-label fw-medium text-secondary">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-secondary">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label fw-medium text-secondary">Mot de passe
                                provisoire</label>
                            <input type="text" class="form-control" id="mot_de_passe" name="mot_de_passe"
                                value="<?= htmlspecialchars(generate_random_password(12)) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="role" class="form-label fw-medium text-secondary">Rôle de l'utilisateur</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="utilisateur" selected>Utilisateur Standard</option>
                                <option value="technicien">Technicien</option>
                                <option value="administrateur">Administrateur</option>
                            </select>
                            <div class="form-text">Détermine les droits d'accès à la plateforme.
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #111827; border: none;">Créer le compte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Modification d'Utilisateur -->
    <?php if ($utilisateur_a_modifier): ?>
        <div class="modal fade show" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
            aria-hidden="true" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #111827;">
                        <h5 class="modal-title" id="editUserModalLabel"><i class="fa-solid fa-user-pen me-2"></i>Modifier le
                            compte</h5>
                        <a href="index.php" class="btn-close btn-close-white" aria-label="Fermer"></a>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editUserForm" action="index.php" method="POST">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="id" value="<?= $utilisateur_a_modifier['id'] ?>">

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="edit_nom" class="form-label fw-medium text-secondary">Nom</label>
                                    <input type="text" class="form-control" id="edit_nom" name="nom"
                                        value="<?= htmlspecialchars($utilisateur_a_modifier['nom']) ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="edit_prenom" class="form-label fw-medium text-secondary">Prénom</label>
                                    <input type="text" class="form-control" id="edit_prenom" name="prenom"
                                        value="<?= htmlspecialchars($utilisateur_a_modifier['prenom']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label fw-medium text-secondary">Adresse Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email"
                                    value="<?= htmlspecialchars($utilisateur_a_modifier['email']) ?>" required>
                            </div>

                            <!-- Ne pas forcer la réécriture du MDP -->
                            <div class="mb-3">
                                <label for="edit_mot_de_passe" class="form-label fw-medium text-secondary">Nouveau mot de
                                    passe (si changement)</label>
                                <input type="text" class="form-control" id="edit_mot_de_passe" name="mot_de_passe"
                                    value="<?= htmlspecialchars(generate_random_password(12)) ?>">
                                <div class="form-text">Ce mot de passe aléatoire prendra effet si vous choisissez de "Mettre
                                    à jour". Laissez vide si vous ne voulez pas le changer.</div>
                            </div>

                            <div class="mb-4">
                                <!-- Ne pas permettre de s'enlever ses propres droits d'admin, pour éviter d'être bloqué -->
                                <?php if ($utilisateur_a_modifier['id'] === $user_info['id']): ?>
                                    <label for="edit_role_disabled" class="form-label fw-medium text-muted">Rôle de
                                        l'utilisateur (Verrouillé pour vous-même)</label>
                                    <input type="hidden" name="role" value="administrateur">
                                    <select class="form-select" id="edit_role_disabled" disabled>
                                        <option value="administrateur" selected>Administrateur</option>
                                    </select>
                                <?php else: ?>
                                    <label for="edit_role" class="form-label fw-medium text-secondary">Rôle de
                                        l'utilisateur</label>
                                    <select class="form-select" id="edit_role" name="role" required>
                                        <option value="utilisateur" <?= ($utilisateur_a_modifier['role'] === 'utilisateur') ? 'selected' : '' ?>>Utilisateur Standard</option>
                                        <option value="technicien" <?= ($utilisateur_a_modifier['role'] === 'technicien') ? 'selected' : '' ?>>Technicien</option>
                                        <option value="administrateur" <?= ($utilisateur_a_modifier['role'] === 'administrateur') ? 'selected' : '' ?>>Administrateur</option>
                                    </select>
                                <?php endif; ?>
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