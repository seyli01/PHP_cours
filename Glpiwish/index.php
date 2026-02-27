<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenue sur le dashboard GLPI</h1>
    <p>Vous êtes connecté en tant que <?php echo $_SESSION['user_email']; ?></p>

    <header>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <?php if($_SESSION['user_role'] === 'administrateur'): ?>
                <li><a href="gestion_materiels.php">Géstion Matériels</a></li>
                <li><a href="gestion_utilisateurs.php">Géstion utilisateurs</a></li>
                    <?php endif; ?>
                <li><a href="incidents.php">Incidents</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
</body>
</html>