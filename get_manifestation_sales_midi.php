<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête SQL pour récupérer le montant des ventes pour la manifestation "1ermai-midi"
$sql_midi = "SELECT SUM(Ventes.prix_total) AS total_midi
             FROM Ventes
             INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
             WHERE Ventes.prix_total != 0.00 AND Paiements.manifestation = '1ermai-midi'";

// Exécution de la requête SQL
$result_midi = $conn->query($sql_midi);

// Initialisation de la variable pour stocker le montant des ventes pour le midi
$totalSalesMidi = 0.00;

// Récupération du montant des ventes pour la manifestation "1ermai-midi"
if ($result_midi->num_rows > 0) {
    $row_midi = $result_midi->fetch_assoc();
    $totalSalesMidi = $row_midi['total_midi'];
}

// Envoi de la réponse brute sans encapsulation JSON
echo number_format($totalSalesMidi, 2);

// Fermeture de la connexion à la base de données
$conn->close();
?>
