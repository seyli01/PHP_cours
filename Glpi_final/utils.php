<?php

//Juste la redirection pour proteger les routes a appeler a chaque routes quon veut proteger (toutes dans ce cas la)
//Faire un fichier pour ca balek mais azy 

function get_base_url()
{
    return '/PHP_cours/Glpi_final';
}

function safe_guarded_route()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header("Location: " . get_base_url() . "/login/index.php");
        exit;
    }
}

function logoutUser()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header("Location: " . get_base_url() . "/login/index.php");
    exit;
}

//fonction pour determiner le type de luser + infos (session ofc)

function who_am_i()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $role = $_SESSION['user_role'] ?? null;

    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'role' => $role,
        'isAdmin' => ($role === 'administrateur'),
        'isTechnicien' => ($role === 'technicien'),
        'isUser' => ($role === 'utilisateur')
    ];
}

//fonction pour proteger les routes
function require_admin()
{
    safe_guarded_route();
    $user = who_am_i();
    if (!$user['isAdmin']) {
        header("Location: " . get_base_url() . "/app/index.php");
        exit;
    }
}

//fonction pour proteger les routes (admin + tech seulement)
function require_tech_or_admin()
{
    safe_guarded_route();
    $user = who_am_i();
    if ($user['isUser']) {
        header("Location: " . get_base_url() . "/app/index.php");
        exit;
    }
}

?>