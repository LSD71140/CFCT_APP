<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête SQL pour compter le nombre de ventes dans la table Ventes avec au moins une ligne dans la table Paiements
$sql = "SELECT COUNT(*) AS total 
        FROM Ventes 
        WHERE prix_total != 0.00 
        AND id_vente IN (SELECT id_vente FROM Paiements)";

$result = $conn->query($sql); // Exécution de la requête SQL

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $salesCount = $row['total'];
    echo $salesCount; // Renvoie le nombre total de ventes
} else {
    echo "0"; // Si aucune vente n'est trouvée, renvoie 0
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
