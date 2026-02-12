<?php 

require 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//1ere etape verifier si le formulaire est soumis et recuprer les donnes du formulaire 
//requete pour voir si le compte existe ou pas dans la base de donnee 
//ensuite verifier si le mot de passe est correct ou pas 

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty($_POST["email"]) || empty($_POST["password"])){
        echo "Veuillez remplir tous les champs.";
    } else {
        $email = trim($_POST["email"]);
        $user_password = trim($_POST["password"]);

        $stmt = $conn->prepare("SELECT * FROM contact WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
            if(password_verify($user_password, $user['password'])){
                $_SESSION['admin'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'isAdmin' => isset($user['isAdmin']) ? (int) $user['isAdmin'] : 0
                ];
                header("Location: index.php");
                exit;
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun compte trouvé avec cet email.";
        }
    }
}

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
</body>
</html>