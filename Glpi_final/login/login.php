<?php

//toujours la meme en vrai de vrai juste ya une gestion derreur un peu meilleure
function handle_auth()
{
    global $mysqli, $error;

    //vide ou pas la session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //traitement du formulaire
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["email"]) || empty($_POST["password"])) {
            $error = "Veuillez remplir tous les champs.";
        } else {
            $email = trim($_POST["email"]);
            $user_password = trim($_POST["password"]);

            //requete sql verif pour l'user
            $stmt = $mysqli->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($user_password, $user['mot_de_passe'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];

                        header("Location: ../app/index.php");
                        exit;
                    } else {
                        $error = "Mot de passe ou email incorrect.";
                    }
                } else {
                    $error = "Aucun compte trouvé avec cet email.";
                }
                $stmt->close();
            } else {
                $error = "Erreur de la base de données.";
            }
        }
    }
}

?>