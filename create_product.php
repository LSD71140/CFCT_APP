<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Produit</title>
	<link rel="stylesheet" type="text/css" href="create_product.css">
</head>
<body>
	<header>
    <a href="index.php" class="btn">ACCUEIL</a>
	<a href="caisse.php" class="btn">CAISSE</a>
  </header>
    <h1>Créer un Produit</h1>
    <h2>Ajouter un Nouveau Produit</h2>
    <form action="process_product.php" method="post">
        <label for="nom_produit">Nom du Produit:</label>
        <input type="text" id="nom_produit" name="nom_produit" required><br><br>
        
        <label for="quantite_stock">Quantité en Stock:</label>
        <input type="number" id="quantite_stock" name="quantite_stock" required><br><br>
        
        <label for="prix_unitaire">Prix Unitaire:</label>
        <input type="number" step="0.01" id="prix_unitaire" name="prix_unitaire" required><br><br>
        
        <input type="submit" value="Créer le Produit">
    </form>
    <hr>
    <h2>Liste des Produits Existants</h2>
    <ul>
        <?php
        // Inclusion du fichier de connexion à la base de données
        require_once 'db_connect.php';

        // Récupération des produits depuis la base de données
        $sql = "SELECT * FROM Produits";
        $result = $conn->query($sql);

        // Affichage des produits existants
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<li>' .$row["id_produit"] . ' - ' . $row["nom_produit"] . ' - ' . $row["quantite_stock"] . ' en stock - ' . $row["prix_unitaire"] . ' €';
                echo ' <a href="update_product.php?id=' . $row["id_produit"] . '">Modifier</a>';
                echo ' <a href="delete_product.php?id=' . $row["id_produit"] . '">Supprimer</a></li>';
            }
        } else {
            echo "Aucun produit trouvé.";
        }
        ?>
    </ul>
</body>
</html>
