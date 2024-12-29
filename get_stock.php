<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Début de la transaction pour s'assurer que toutes les mises à jour sont atomiques
$conn->begin_transaction();

try {
    // Mettre à jour les quantités en stock
    $updateStockSql = "
        UPDATE Produits p
        LEFT JOIN (
            SELECT id_produit, SUM(quantite) AS quantite_vendue
            FROM VenteProduits
            WHERE id_vente IN (SELECT id_vente FROM Paiements)
            GROUP BY id_produit
        ) v ON p.id_produit = v.id_produit
        SET p.stock_restant = p.quantite_stock - COALESCE(v.quantite_vendue, 0)
    ";
    
    if (!$conn->query($updateStockSql)) {
        throw new Exception("Erreur lors de la mise à jour du stock : " . $conn->error);
    }

    // Récupération des données de stock pour affichage
    $selectStockSql = "
        SELECT 
            nom_produit, 
            stock_restant,
            COALESCE((
                SELECT SUM(VenteProduits.quantite) 
                FROM VenteProduits 
                WHERE VenteProduits.id_produit = Produits.id_produit 
                      AND VenteProduits.id_vente IN (SELECT id_vente FROM Paiements)
            ), 0) AS quantite_vendue
        FROM Produits
    ";
    
    $result = $conn->query($selectStockSql);

    $stocks = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stocks[] = array(
                'nom_produit' => $row['nom_produit'],
                'stock_restant' => $row['stock_restant'],
                'quantite_vendue' => $row['quantite_vendue']
            );
        }
    }

    // Conversion du tableau en format JSON
    echo json_encode($stocks);

    // Commit de la transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback de la transaction en cas d'erreur
    $conn->rollback();
    echo json_encode(array('error' => $e->getMessage()));
}

// Fermer la connexion à la base de données
$conn->close();
?>
