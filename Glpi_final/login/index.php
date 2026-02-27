<?php
require_once '../db.php';
require_once 'login.php';
$error = null;
handle_auth();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../app.css">
</head>

<body>
    <div class="login-container">
        <h1>Page de login</h1>

        <?php if (!empty($error)) {
            echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        } ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="submit-btn">Se connecter</button>
        </form>
    </div>
</body>

</html>