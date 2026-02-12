<?php

/*
$students = ["Alice", "Bob", "Charlie", "David", "Eve"];

$students[] = "Frank";

array_unshift($students, "Zara");

$students_copy = $students;

array_splice($students, 2, 1);

echo "<pre>";
echo "Tableau après suppression (original modifié):\n";
print_r($students);
echo "\nCopie (avant suppression):\n";
print_r($students_copy);
echo "</pre>";



$produits = [
    "nom" => "Ordinateur Portable",
    "marque" => "Dell",
    "prix" => 1200,
    "stock" => 30
]; 

foreach ($produits as $key => $value) {
    echo "<p>$key : $value</p>";
}

*/

$test = [
    ["nom" => "Alice", "age" => 20, "ville" => "Paris"],
    ["nom" => "Bob", "age" => 22, "ville" => "Lyon"],
    ["nom" => "Charlie", "age" => 23, "ville" => "Marseille"]
];

foreach ($test as $i => $s) {
    echo "<p>Étudiant ".($i+1)." : {$s['nom']} — {$s['age']} ans — {$s['ville']}</p>";
}


?>

