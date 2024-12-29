<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Récupération des données envoyées depuis le formulaire
$totalAmount = isset($_POST['totalAmount']) ? floatval($_POST['totalAmount']) : null;
$cartData = isset($_POST['cartData']) ? json_decode($_POST['cartData'], true) : null;

// Vérification si les données sont valides
if ($totalAmount !== null && $cartData !== null) {
    // Commencer une transaction
    $conn->begin_transaction();

    try {
        // Insérer la vente dans la table Ventes
        $sql = "INSERT INTO Ventes (prix_total, date_vente) VALUES (?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("d", $totalAmount);
        $stmt->execute();
        
        // Récupérer l'id de la vente insérée
        $idVente = $conn->insert_id;

        // Insérer les produits vendus dans la table VenteProduits
        $sql = "INSERT INTO VenteProduits (id_vente, id_produit, nom_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($cartData as $item) {
            $stmt->bind_param("iisdd", $idVente, $item['id_produit'], $item['nom_produit'], $item['quantite'], $item['prix_unitaire']);
            $stmt->execute();
        }

        // Valider la transaction
        $conn->commit();

        // Envoyer une réponse de succès
        echo "Vente enregistrée avec succès !";
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $conn->rollback();
        echo "Erreur lors de l'enregistrement de la vente : " . $e->getMessage();
    }
} else {
    echo "Données manquantes ou invalides dans la requête.";
}
?>
