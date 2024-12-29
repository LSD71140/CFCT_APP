<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Vérification si l'identifiant du produit est présent dans l'URL
if (isset($_GET['id'])) {
    $id_produit = $_GET['id'];

    // Récupération des informations sur le produit à partir de son identifiant
    $sql = "SELECT * FROM Produits WHERE id_produit = $id_produit";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nom_produit = $row['nom_produit'];
        $quantite_stock = $row['quantite_stock'];
        $prix_unitaire = $row['prix_unitaire'];
    } else {
        echo "Aucun produit trouvé avec cet identifiant.";
        exit;
    }
} else {
    echo "Identifiant du produit non spécifié.";
    exit;
}

// Traitement de la soumission du formulaire de mise à jour du produit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom_produit = $_POST['nom_produit'];
    $quantite_stock = $_POST['quantite_stock'];
    $prix_unitaire = $_POST['prix_unitaire'];

    // Préparation de la requête de mise à jour
    $sql = "UPDATE Produits SET nom_produit = '$nom_produit', quantite_stock = $quantite_stock, prix_unitaire = $prix_unitaire WHERE id_produit = $id_produit";

    // Exécution de la requête
    if ($conn->query($sql) === TRUE) {
        echo "Le produit a été mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du produit : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Produit</title>
</head>
<body>
    <h1>Modifier un Produit</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_produit; ?>" method="post">
        <label for="nom_produit">Nom du Produit:</label>
        <input type="text" id="nom_produit" name="nom_produit" value="<?php echo $nom_produit; ?>" required><br><br>
        
        <label for="quantite_stock">Quantité en Stock:</label>
        <input type="number" id="quantite_stock" name="quantite_stock" value="<?php echo $quantite_stock; ?>" required><br><br>
        
        <label for="prix_unitaire">Prix Unitaire:</label>
        <input type="text" id="prix_unitaire" name="prix_unitaire" value="<?php echo $prix_unitaire; ?>" required><br><br>
        
        <input type="submit" value="Mettre à Jour le Produit">
    </form>
</body>
</html>
