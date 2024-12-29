<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Requête pour récupérer l'ID de vente et le prix_total de la vente la plus récente
$sql = "SELECT id_vente, prix_total FROM Ventes ORDER BY id_vente DESC LIMIT 1";
$result = $conn->query($sql);

// Vérification si la requête a réussi
if ($result && $result->num_rows > 0) {
    // Récupération de l'ID de vente et du prix_total
    $row = $result->fetch_assoc();
    $idVente = $row['id_vente'];
    $prixTotal = $row['prix_total'];

    // Création d'un tableau associatif pour stocker les données
    $response = array(
        'id_vente' => $idVente,
        'prix_total' => $prixTotal
    );

    // Conversion du tableau en format JSON et envoi de la réponse
    echo json_encode($response);
} else {
    // En cas d'erreur ou si aucune vente n'a été trouvée, envoyer une réponse vide ou un message d'erreur
    $response = array(
        'id_vente' => null, // Si aucune vente n'a été trouvée
        'prix_total' => 0.00 // Par défaut, si aucune vente n'a été trouvée
    );

    // Conversion du tableau en format JSON et envoi de la réponse
    echo json_encode($response);
}
?>
