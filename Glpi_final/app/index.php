<?php
require_once '../utils.php';
safe_guarded_route(); //protection contre du coup tentative dacces par url

$user_info = who_am_i(); // admin, tech ou utilisateur lambda 

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GLPI Final</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons (Optional but nice) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../app.css">
</head>

<body class="app-body">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-desktop me-2"></i> GLPI Final
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
                    </li>
                    <?php if ($user_info && $user_info['isAdmin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../app/equipement/index.php">Gestion Matériels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../app/utilisateurs/index.php">Gestion Utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/incidents/index.php">Incidents</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? 'Utilisateur'); ?>
                    </span>
                    <a href="../login/logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Bienvenue sur le dashboard</h1>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Aperçu rapide</h5>
                        <p class="card-text">Vous êtes connecté en tant que
                            <strong><?php echo htmlspecialchars($user_info['role'] ?? 'utilisateur'); ?></strong>.
                        </p>
                        <hr>
                        <p class="text-muted">Utilisez le menu de navigation en haut pour accéder aux différentes
                            sections de l'application.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>