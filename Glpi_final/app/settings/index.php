<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../utils.php';
require_once __DIR__ . '/profile.php';

safe_guarded_route();

$user_info = who_am_i();
$profile = fetch_user_profile($mysqli);
$message = update_user_profile($mysqli);

if ($message === true) {
    $profile = fetch_user_profile($mysqli);
}

// Oui javoue cets l'ia 
$role_badge = [
    'administrateur' => ['label' => 'Administrateur', 'class' => 'bg-danger'],
    'technicien'     => ['label' => 'Technicien',     'class' => 'bg-warning text-dark'],
    'utilisateur'    => ['label' => 'Utilisateur',    'class' => 'bg-secondary'],
];
$role_info = $role_badge[$profile['role'] ?? ''] ?? ['label' => ucfirst($profile['role'] ?? 'Inconnu'), 'class' => 'bg-secondary'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - GLPI Final</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../app.css">
</head>
<body class="app-body">

    <!-- Navbar -->
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
                        <a class="nav-link" href="../incidents/index.php">Incidents</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? 'Utilisateur'); ?>
                    </span>
                    <a href="index.php" class="btn btn-outline-light btn-sm me-2 active" title="Paramètres">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    <a href="../../login/logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="fw-bold">Mon profil</h1>
                <p class="text-muted">Consultez et modifiez vos informations personnelles.</p>
            </div>
        </div>

        <?php if ($message === true): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Profil mis à jour avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (is_string($message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars($profile['avatar_url']) ?>" alt="Avatar"
                                    class="rounded-circle border"
                                    style="width:64px;height:64px;object-fit:cover;">
                                <h5 class="fw-semibold mb-0">
                                    <?= htmlspecialchars(trim(($profile['prenom'] ?? '') . ' ' . ($profile['nom'] ?? ''))) ?>
                                </h5>
                            </div>
                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fa-solid fa-pen me-1"></i> Modifier
                            </button>
                        </div>

                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="small fw-semibold text-uppercase text-muted mb-1">Prénom</div>
                                <div class="fw-medium"><?= htmlspecialchars($profile['prenom'] ?? '—') ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small fw-semibold text-uppercase text-muted mb-1">Nom</div>
                                <div class="fw-medium"><?= htmlspecialchars($profile['nom'] ?? '—') ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small fw-semibold text-uppercase text-muted mb-1">Adresse e-mail</div>
                                <div class="fw-medium"><?= htmlspecialchars($profile['email'] ?? '—') ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small fw-semibold text-uppercase text-muted mb-1">Rôle</div>
                                <div>
                                    <span class="badge <?= $role_info['class'] ?> px-2">
                                        <?= $role_info['label'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small fw-semibold text-uppercase text-muted mb-1">Identifiant</div>
                                <div class="fw-medium text-muted">#<?= (int)($profile['id'] ?? 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modifier le profil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #111827;">
                    <h5 class="modal-title" id="editProfileModalLabel">
                        <i class="fa-solid fa-user-pen me-2"></i> Modifier mon profil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="modifier_profil">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary">Photo de profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars($profile['avatar_url']) ?>" alt="Avatar actuel"
                                    class="rounded-circle border"
                                    style="width:56px;height:56px;object-fit:cover;">
                                <input type="file" class="form-control" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                            <div class="form-text">JPG, PNG, GIF ou WEBP — max 2 Mo. Laissez vide pour garder l'actuelle.</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="edit_nom" class="form-label fw-medium text-secondary">Nom</label>
                                <input type="text" class="form-control" id="edit_nom" name="nom"
                                    value="<?= htmlspecialchars($profile['nom'] ?? '') ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="edit_prenom" class="form-label fw-medium text-secondary">Prénom</label>
                                <input type="text" class="form-control" id="edit_prenom" name="prenom"
                                    value="<?= htmlspecialchars($profile['prenom'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label fw-medium text-secondary">Adresse e-mail</label>
                            <input type="email" class="form-control" id="edit_email" name="email"
                                value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_mdp" class="form-label fw-medium text-secondary">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="edit_mdp" name="mot_de_passe"
                                placeholder="Laisser vide pour ne pas changer" autocomplete="new-password">
                            <div class="form-text">Laissez vide si vous ne souhaitez pas modifier votre mot de passe.</div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #111827; border: none;">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>