<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête SQL pour récupérer le montant des ventes pour la manifestation "1ermai-soir"
$sql_soir = "SELECT SUM(Ventes.prix_total) AS total_soir
             FROM Ventes
             INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
             WHERE Ventes.prix_total != 0.00 AND Paiements.manifestation = '1ermai-soir'";

// Exécution de la requête SQL
$result_soir = $conn->query($sql_soir);

// Initialisation de la variable pour stocker le montant des ventes pour le soir
$totalSalesSoir = 0.00;

// Récupération du montant des ventes pour la manifestation "1ermai-soir"
if ($result_soir->num_rows > 0) {
    $row_soir = $result_soir->fetch_assoc();
    $totalSalesSoir = $row_soir['total_soir'];
}

// Envoi de la réponse brute sans encapsulation JSON
echo number_format($totalSalesSoir, 2);

// Fermeture de la connexion à la base de données
$conn->close();
?>
