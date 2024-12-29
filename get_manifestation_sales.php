<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête SQL pour récupérer les montants des ventes pour les manifestations "1ermai-midi" et "1ermai-soir"
$sql_midi = "SELECT SUM(Ventes.prix_total) AS total_midi
             FROM Ventes
             INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
             WHERE Ventes.prix_total != 0.00 AND Paiements.manifestation = '1ermai-midi'";

$sql_soir = "SELECT SUM(Ventes.prix_total) AS total_soir
             FROM Ventes
             INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
             WHERE Ventes.prix_total != 0.00 AND Paiements.manifestation = '1ermai-soir'";

// Exécution des requêtes SQL
$result_midi = $conn->query($sql_midi);
$result_soir = $conn->query($sql_soir);

// Initialisation des variables pour stocker les montants des ventes
$totalSalesMidi = 0.00;
$totalSalesSoir = 0.00;

// Récupération des montants des ventes pour la manifestation "1ermai-midi"
if ($result_midi->num_rows > 0) {
    $row_midi = $result_midi->fetch_assoc();
    $totalSalesMidi = $row_midi['total_midi'];
}

// Récupération des montants des ventes pour la manifestation "1ermai-soir"
if ($result_soir->num_rows > 0) {
    $row_soir = $result_soir->fetch_assoc();
    $totalSalesSoir = $row_soir['total_soir'];
}

// Création d'un tableau associatif contenant les montants des ventes pour chaque manifestation
$manifestationSales = array(
    "midi" => $totalSalesMidi,
    "soir" => $totalSalesSoir
);

// Conversion du tableau en format JSON
$jsonResponse = json_encode($manifestationSales);

// Envoi de la réponse JSON
echo $jsonResponse;

// Fermeture de la connexion à la base de données
$conn->close();
?>
