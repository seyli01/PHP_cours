<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if (empty($_POST["username"]) || empty($_POST["password"])){
        echo "<p>Veuillez remplir tous les champs.</p>";
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $login = ['username'=> $username,'password'=> $password];
    print_r($login);
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
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" value="Submit">
    </form>
</body>
</html>