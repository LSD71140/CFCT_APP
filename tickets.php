<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Ventes</title>
    <link rel="stylesheet" href="tickets.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <h1>Liste des Ventes</h1>
    <div class="navigation">
        <a href="index.php" class="btn">ACCUEIL</a>
        <a href="stock.php" class="btn">STOCKS</a>
        <a href="caisse.php" class="btn">CAISSE</a>
    </div>
    <div class="ventes">
        <!-- Affichage des ventes validées -->
        <?php
        // Inclure le fichier de connexion à la base de données
        include 'db_connect.php';

        // Récupérer les ventes validées depuis la base de données
        $query = "SELECT Ventes.id_vente, Ventes.prix_total, Produits.nom_produit, Produits.prix_unitaire, VenteProduits.quantite,
                    Paiements.montant_cartes, Paiements.montant_especes, Paiements.montant_cheque, Paiements.date_paiement
                    FROM Ventes
                    INNER JOIN VenteProduits ON Ventes.id_vente = VenteProduits.id_vente
                    INNER JOIN Produits ON VenteProduits.id_produit = Produits.id_produit
                    INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
                    ORDER BY Ventes.id_vente DESC";

        $result = mysqli_query($conn, $query);

        // Initialisation du tableau pour stocker les ventes par leur numéro de vente
        $ventes_groupées = array();

        // Récupérer les données et les stocker dans le tableau $ventes_groupées
        while ($row = mysqli_fetch_assoc($result)) {
            $num_vente = $row['id_vente'];
            if (!isset($ventes_groupées[$num_vente])) {
                // Initialiser un tableau vide pour stocker les articles de la vente
                $ventes_groupées[$num_vente] = array(
                    'numero_vente' => $num_vente,
                    'articles' => array(),
                    'prix_total' => $row['prix_total'],
                    'montant_cartes' => $row['montant_cartes'],
                    'montant_especes' => $row['montant_especes'],
                    'montant_cheque' => $row['montant_cheque'],
                    'date_paiement' => $row['date_paiement']
                );
            }

            // Ajouter l'article à la vente
            $ventes_groupées[$num_vente]['articles'][] = array(
                'nom_produit' => htmlspecialchars($row['nom_produit']),
                'quantite' => $row['quantite'],
                'prix_unitaire' => $row['prix_unitaire']
            );
        }

        // Afficher les ventes sous forme de liste
        foreach ($ventes_groupées as $vente) {
            echo "<div class='vente'>";
            // Affichage des détails de la vente
            echo "<span class='numero-vente'>Numéro de Vente: " . $vente['numero_vente'] . "</span><br>";
            echo "<span class='date_paiement'> " . $vente['date_paiement'] . "</span><br>";
            echo "<span class='articles'>Articles:<br>";
            foreach ($vente['articles'] as $article) {
                echo "- " . $article['nom_produit'] . " (Quantité: " . $article['quantite'] . ", Prix unitaire: " . $article['prix_unitaire'] . ")<br>";
            }
            echo "</span>";
            echo "<span class='montant-total'>TOTAL: " . $vente['prix_total'] . "</span><br>";
            // Affichage des montants de carte, espèces et chèque
            if ($vente['montant_cartes'] > 0) {
                echo "<span class='montant-carte'>Montant carte: " . $vente['montant_cartes'] . "</span><br>";
            }
            if ($vente['montant_especes'] > 0) {
                echo "<span class='montant-espece'>Montant espèces: " . $vente['montant_especes'] . "</span><br>";
            }
            if ($vente['montant_cheque'] > 0) {
                echo "<span class='montant-cheque'>Montant chèque: " . $vente['montant_cheque'] . "</span><br>";
            }
            // Formulaires pour imprimer les tickets
            echo "<form action='imprimer_ticket_caisse.php' method='post' class='ticket-form'>";
            echo "<input type='hidden' name='id_vente' value='" . $vente['numero_vente'] . "'>";
            echo "<button type='submit' class='ticket-btn'>Imprimer Ticket de Caisse</button>";
            echo "</form>";
            echo "<form action='imprimer_ticket_par_articles.php' method='post' class='ticket-form'>";
            echo "<input type='hidden' name='id_vente' value='" . $vente['numero_vente'] . "'>";
            echo "<button type='submit' class='ticket-btn'>Imprimer Ticket par Articles</button>";
            echo "</form>";
            echo "</div><br>";
        }
        // Si aucune vente trouvée
        if (empty($ventes_groupées)) {
            echo "Aucune vente trouvée.";
        }
        ?>
    </div>

    <script>
        // Votre script JavaScript peut être conservé pour d'autres fonctionnalités
    </script>
</body>
</html>
