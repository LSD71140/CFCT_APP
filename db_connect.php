<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'cfct', 'Cronat71140', 'cfct_app');

// Vérification de la connexion
if ($conn->connect_error) {
    die('Erreur de connexion à la base de données : ' . $conn->connect_error);
}
?>
