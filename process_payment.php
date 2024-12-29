<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Récupération des données envoyées depuis le formulaire
$totalAmount = isset($_POST['totalAmount']) ? floatval($_POST['totalAmount']) : null;
$paymentMethod = isset($_POST['paymentMethod']) ? htmlspecialchars($_POST['paymentMethod']) : null;
$itemNames = isset($_POST['itemName']) ? $_POST['itemName'] : [];
$itemQuantities = isset($_POST['itemQuantity']) ? $_POST['itemQuantity'] : [];
$itemPrices = isset($_POST['itemPrice']) ? $_POST['itemPrice'] : [];

// Affichage des données reçues pour vérification
echo "Données reçues : <br>";
echo "Total Amount: " . $totalAmount . "<br>";
echo "Payment Method: " . $paymentMethod . "<br>";
echo "Item Names: " . implode(', ', $itemNames) . "<br>";
echo "Item Quantities: " . implode(', ', $itemQuantities) . "<br>";
echo "Item Prices: " . implode(', ', $itemPrices) . "<br>";

// Vérification des données
if ($totalAmount !== null && $paymentMethod !== null && !empty($itemNames) && !empty($itemQuantities) && !empty($itemPrices) && count($itemNames) === count($itemQuantities) && count($itemNames) === count($itemPrices)) {
    // Enregistrement de la vente dans la base de données
    $sql = "INSERT INTO Ventes (montant_total, methode_paiement) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ds", $totalAmount, $paymentMethod);

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
