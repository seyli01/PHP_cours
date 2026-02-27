
<?php
require 'db.php';
require_once 'logic.php';
$error = null;
handle_auth();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
</head>
<body>
    <h1>Formulaire d'authentification</h1>
    <form action="auth.php" method="post">
        <label for="email">Email :</label>
        <input type="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required>

        <button type="submit" name="login" >Se connecter</button>
    </form>
    <?php if (!empty($error)) { echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>'; } ?>
</body>
</html>