<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Récupération des données envoyées depuis la requête AJAX
$data = json_decode(file_get_contents("php://input"), true);

if ($data !== null && isset($data['firstPaymentMethod'], $data['firstAmount'], $data['secondPaymentMethod'], $data['secondAmount'], $data['cartData'])) {
    // Extraction des données
    $firstPaymentMethod = htmlspecialchars($data['firstPaymentMethod']); // Nettoyez et validez le premier moyen de paiement
    $firstAmount = floatval($data['firstAmount']); // Assurez-vous que le montant est de type float pour le premier paiement
    $secondPaymentMethod = htmlspecialchars($data['secondPaymentMethod']); // Nettoyez et validez le deuxième moyen de paiement
    $secondAmount = floatval($data['secondAmount']); // Assurez-vous que le montant est de type float pour le deuxième paiement
    $cartData = $data['cartData'];

    // Enregistrement de la vente dans la base de données
    $sql = "INSERT INTO Ventes (moyen_paiement_1, montant_paiement_1, moyen_paiement_2, montant_paiement_2) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsd", $firstPaymentMethod, $firstAmount, $secondPaymentMethod, $secondAmount);

    if ($stmt->execute()) {
        $ventaId = $stmt->insert_id; // Récupération de l'identifiant de la vente enregistrée
        
        // Enregistrement des détails de la vente (produits vendus) dans la table VenteProduits
        foreach ($cartData as $item) {
            $productName = htmlspecialchars($item['name']); // Nettoyez et validez le nom du produit
            $quantity = intval($item['quantity']); // Assurez-vous que la quantité est un entier
            $price = floatval($item['price']); // Assurez-vous que le prix est de type float

            $sql = "INSERT INTO VenteProduits (id_vente, nom_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issd", $ventaId, $productName, $quantity, $price);
            $stmt->execute();
        }

        echo "Vente et produits enregistrés avec succès !";
    } else {
        echo "Erreur lors de l'enregistrement de la vente : " . $conn->error;
    }

    // Fermeture de la connexion et fin de script
    $stmt->close();
    $conn->close();
} else {
    echo "Données manquantes ou invalides dans la requête.";
}
?>
