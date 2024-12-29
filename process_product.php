<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom_produit = $_POST['nom_produit'];
    $quantite_stock = $_POST['quantite_stock'];
    $prix_unitaire = $_POST['prix_unitaire'];

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO Produits (nom_produit, quantite_stock, prix_unitaire) VALUES ('$nom_produit', $quantite_stock, $prix_unitaire)";

    // Exécution de la requête
    if ($conn->query($sql) === TRUE) {
        // Affichage du message de succès dans la fenêtre parente (create_product.php)
        echo "<script>window.onload = function() { alert('Le produit a été ajouté avec succès.'); }</script>";
    } else {
        // Affichage du message d'erreur dans la fenêtre parente (create_product.php)
        echo "<script>window.onload = function() { alert('Erreur lors de l\'ajout du produit : " . $conn->error . "'); }</script>";
    }

    // Attends 5 secondes avant de rediriger vers la page create_product.php
    echo "<script>setTimeout(function(){ window.location.href = 'create_product.php'; }, 1000);</script>";
}
?>
