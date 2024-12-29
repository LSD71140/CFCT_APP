<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête SQL pour récupérer le total des ventes depuis la table Ventes avec une jointure sur la table Paiements pour obtenir le nom de la manifestation
$sql = "SELECT SUM(Ventes.prix_total) AS total
        FROM Ventes
        INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
        WHERE Ventes.prix_total != 0.00";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSales = $row['total'];
    echo number_format($totalSales, 2); // Renvoie le total des ventes avec deux décimales
} else {
    echo "0.00"; // Si aucune vente n'est trouvée, renvoie 0.00
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
