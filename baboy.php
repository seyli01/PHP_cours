<?php
session_start();
var_dump($_SESSION);
// session une boîte pour stocker des données temporaire 
// durant votre navigation
$user = [];
if($_SERVER["REQUEST_METHOD"] == "POST"){

    //isset / empty / 
    if(empty($_POST['username']) || empty($_POST['password'])){
        echo "Veuillez remplir tous les champs";
        //exit;
    }
    // récupération des données stockées dans superglobale pour les stocker dans un tableau
   $user = createUser();
    // créer ou le stockage des données dans la session
   $_SESSION["utilisateur"] = $user;

}
//FUNCTION
function createUser(){
return $user = [
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];
}

function Afficher($user){
    foreach($user as $key => $value){
            if($key === "username"){

                 echo "<h1>$key : $value</h1>";
            }else{
                 echo "<p>$key : $value</p>";
            }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text"  name="username"><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password"><br><br>
        <input type="submit" value="Submit">
    </form>

    <div>
     <?php 
        if(!empty($user)){
            Afficher($user);
        }
     ?>
    </div>

</body>
</html>