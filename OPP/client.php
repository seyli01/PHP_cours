<?php
class Client {
    //mot cle class pour definir une classe
    //suivi par le nom de la classe (Client)
    //on declare les attributs ou proprietes de la classe (id, nom, prenom, email, numerotel, adresse, actif)
    //on definit l'accessibilite de chaque attribut (public, private, protected)
    //typer les données des attributs (string, int, bool, etc.)

    private string $id_client;
    private string $nom;
    private string $prenom;
    private string $email;
    private int $telephone;
    private string $adresse;
    private int $age;
    private bool $actif;

    public function __construct(string $nom, string $prenom, string $email, int $telephone, string $adresse, int $age, bool $actif) {
        $this->id_client = uniqid();
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
        $this->age = $age;
        $this->actif = $actif;
    }
    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getActif() {
        return $this->actif;
    }

    public function getAge() {
        return $this->age;
    }
}

/*

$objectClient1 = new Client("Doe", "John", "john.doe@example.com", 1234567890, "123 Main St", 30, true);
echo "ID Client: " . $objectClient1->id_client . "<br>";
echo "Nom: " . $objectClient1->nom . "<br>";
echo "Prenom: " . $objectClient1->prenom . "<br>";
echo "Email: " . $objectClient1->email . "<br>";
echo "Telephone: " . $objectClient1->telephone . "<br>";
echo "Adresse: " . $objectClient1->adresse . "<br>";
echo "Age: " . $objectClient1->age . "<br>";
echo "Actif: " . ($objectClient1->actif ? "Oui" : "Non") . "<br>";

$objectClient2 = new Client("Smith", "Jane", "jane.smith@example.com", 987654321, "456 Oak St", 25, false);
echo "ID Client: " . $objectClient2->id_client . "<br>";
echo "Nom: " . $objectClient2->nom . "<br>";
echo "Prenom: " . $objectClient2->prenom . "<br>";
echo "Email: " . $objectClient2->email . "<br>";
echo "Telephone: " . $objectClient2->telephone . "<br>";
echo "Adresse: " . $objectClient2->adresse . "<br>";
echo "Age: " . $objectClient2->age . "<br>";
echo "Actif: " . ($objectClient2->actif ? "Oui" : "Non") . "<br>";

//erreurs silencieuses : les erreurs ne sont pas affichées, mais elles peuvent causer des problèmes dans le code
//erreurs fatales : les erreurs qui arrêtent l'exécution du script


*/