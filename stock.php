<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="stock.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>
    <h1>Stock des produits</h1>

    <div class="main-content">
        <div class="counters">
            <div class="counter">
                <h2>Nombre de ventes</h2>
                <p id="ventes-count">0</p>
            </div>
            <div class="counter">
                <h2>Total des ventes</h2>
                <p id="total-ventes">0.00 €</p>
            </div>
            <div class="counter">
                <h2>Ventes 1er mai (midi)</h2>
                <p id="ventes-midi">0.00 €</p>
            </div>
            <div class="counter">
                <h2>Ventes 1er mai (soir)</h2>
                <p id="ventes-soir">0.00 €</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nom du produit</th>
                    <th>Stock restant</th>
                    <th>Quantité vendue</th>
                </tr>
            </thead>
            <tbody id="stock-list">
                <!-- Les stocks seront affichés ici dynamiquement -->
            </tbody>
        </table>
    </div>

    <footer>
        <a href="index.php" class="btn">ACCUEIL</a>
        <a href="caisse.php" class="btn">CAISSE</a>
        <a href="create_product.php" class="btn">GESTION DES PRODUITS</a>
    </footer>

    <script>
        $(document).ready(function() {
            updateStock(); // Appel initial pour afficher les stocks lors du chargement de la page
            updateSalesCount(); // Appel initial pour afficher le nombre de ventes
            updateTotalSales(); // Appel initial pour afficher le total des ventes
            updateManifestationSales(); // Appel initial pour afficher les ventes des manifestations

            // Fonction pour mettre à jour les stocks en temps réel
            function updateStock() {
                $.ajax({
                    type: 'GET',
                    url: 'get_stock.php',
                    dataType: 'json',
                    success: function(response) {
                        displayStock(response); // Afficher les stocks sur la page
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Fonction pour afficher les stocks sur la page
            function displayStock(stocks) {
                var stockList = document.getElementById('stock-list');
                stockList.innerHTML = ''; // Vider la liste des stocks avant de l'actualiser

                stocks.forEach(function(stock) {
                    var row = document.createElement('tr');
                    row.innerHTML = '<td>' + stock.nom_produit + '</td><td>' + stock.stock_restant + '</td><td>' + stock.quantite_vendue + '</td>';
                    stockList.appendChild(row);
                });
            }

            // Fonction pour mettre à jour le nombre de ventes
            function updateSalesCount() {
                $.ajax({
                    type: 'GET',
                    url: 'get_sales_count.php',
                    success: function(response) {
                        $('#ventes-count').text(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Fonction pour mettre à jour le total des ventes
            function updateTotalSales() {
                $.ajax({
                    type: 'GET',
                    url: 'get_total_sales.php',
                    success: function(response) {
                        $('#total-ventes').text(response + ' €');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Fonction pour mettre à jour les ventes des manifestations (midi et soir)
            function updateManifestationSales() {
                // Appel à la fonction updateManifestationSalesMidi pour les ventes du midi
                $.ajax({
                    type: 'GET',
                    url: 'get_manifestation_sales_midi.php',
                    success: function(response) {
                        $('#ventes-midi').text(response + ' €');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                // Appel à la fonction updateManifestationSalesSoir pour les ventes du soir
                $.ajax({
                    type: 'GET',
                    url: 'get_manifestation_sales_soir.php',
                    success: function(response) {
                        $('#ventes-soir').text(response + ' €');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Mettre à jour les stocks automatiquement toutes les X secondes (par exemple, toutes les 5 secondes)
            setInterval(updateStock, 5000);
            setInterval(updateSalesCount, 5000);
            setInterval(updateTotalSales, 5000);
            setInterval(updateManifestationSales, 5000);
        });
    </script>
</body>
</html>
