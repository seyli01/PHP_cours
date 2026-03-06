<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'list';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Afficher les vélos</a></li>
                <li><a href="index.php?page=ajout">Ajouter un vélo</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        switch($page) {
            case 'ajout':
                require_once 'views/formadd.php';
                break;
            case 'list':
                require_once 'views/listvelos.php';
                break;
            default:
                require_once 'views/404.php';
                break;
        }
        ?>
    </main>
</body>
</html>