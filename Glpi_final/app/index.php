<?php
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/incidents/incidents.php';
require_once __DIR__ . '/equipement/equipment.php';

safe_guarded_route(); //protection contre du coup tentative dacces par url

$user_info = who_am_i(); // admin, tech ou utilisateur lambda 

// 1. Nombre de tickets (selon le rôle)
$incidents_visibles = get_incidents_for_user($mysqli, $user_info);
$nb_tickets = count($incidents_visibles);

// 2. Nombre de matériels (selon le rôle)
$nb_materiels = 0;
if ($user_info['role'] === 'administrateur') {
    $nb_materiels = count(get_all_equipments($mysqli));
} else {
    // Tech et User voient ce qui leur est assigné
    $nb_materiels = get_equipment_count_by_user($mysqli, $user_info['id']);
}

// 3. Nombre d'utilisateurs (Admin seulement)
$nb_users = 0;
if ($user_info['role'] === 'administrateur') {
    $nb_users = count(get_all_users($mysqli));
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GLPI Final</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
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
                            <a class="nav-link" href="equipement/index.php">Gestion Matériels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="utilisateurs/index.php">Gestion Utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="incidents/index.php">Incidents</a>
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
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="fw-bold">Tableau de bord</h1>
                <p class="text-muted">Bonjour <?= htmlspecialchars($user_info['email']) ?>, voici l'état actuel de votre
                    parc.</p>
            </div>
        </div>

        <!-- Cartes Statistiques -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            <!-- Carte Tickets -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #007bff !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fa-solid fa-ticket-alt text-primary fs-4"></i>
                            </div>
                            <span
                                class="badge bg-light text-primary border border-primary-subtle rounded-pill">Actifs</span>
                        </div>
                        <h3 class="card-title fw-bold mb-1"><?= $nb_tickets ?></h3>
                        <p class="card-text text-muted mb-0">Total des tickets</p>
                    </div>
                </div>
            </div>

            <!-- Carte Matériels -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fa-solid fa-laptop text-success fs-4"></i>
                            </div>
                            <span
                                class="badge bg-light text-success border border-success-subtle rounded-pill">Inventaire</span>
                        </div>
                        <h3 class="card-title fw-bold mb-1"><?= $nb_materiels ?></h3>
                        <p class="card-text text-muted mb-0">Matériels assignés</p>
                    </div>
                </div>
            </div>

            <!-- Carte Utilisateurs (Uniquement Admin) -->
            <?php if ($user_info['role'] === 'administrateur'): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #ffc107 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fa-solid fa-users text-warning fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-light text-warning border border-warning-subtle rounded-pill">Comptes</span>
                            </div>
                            <h3 class="card-title fw-bold mb-1"><?= $nb_users ?></h3>
                            <p class="card-text text-muted mb-0">Utilisateurs inscrits</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>