<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Vérification si l'identifiant du produit à supprimer a été passé en paramètre dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Récupération de l'identifiant du produit à supprimer depuis l'URL
    $id_produit = $_GET['id'];

    // Préparation de la requête de suppression
    $sql = "DELETE FROM Produits WHERE id_produit = $id_produit";

    // Exécution de la requête
    if ($conn->query($sql) === TRUE) {
        // Redirection vers la page create_product.php après la suppression
        header("Location: create_product.php");
        exit; // Arrêt de l'exécution du script après la redirection
    } else {
        // En cas d'erreur, affichage d'un message d'erreur
        echo "Erreur lors de la suppression du produit : " . $conn->error;
    }
} else {
    // Si l'identifiant du produit n'est pas passé en paramètre dans l'URL, affichage d'un message d'erreur
    echo "Identifiant du produit non spécifié.";
}
?>
