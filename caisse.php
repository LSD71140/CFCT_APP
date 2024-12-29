<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point de Vente</title>
    <link rel="stylesheet" href="caisse.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>
    <header>
        <h1>Point de Vente</h1>
    </header>
    <main>
        <div class="products">
            <?php
            // Inclusion du fichier de connexion à la base de données
            require_once 'db_connect.php';

            // Récupération des produits depuis la base de données
            $sql = "SELECT * FROM Produits";
            $result = $conn->query($sql);

            // Affichage des boutons de produits
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Vérifiez si le stock restant est à zéro
                    $class = $row["stock_restant"] > 0 ? "" : "out-of-stock";
                    echo '<button class="product-btn ' . $class . '" onclick="addProduct(' . $row["id_produit"] . ', \'' . $row["nom_produit"] . '\', ' . $row["prix_unitaire"] . ')">' . $row["nom_produit"] . '</button>';
                }
            } else {
                echo "Aucun produit trouvé.";
            }
            ?>
        </div>
        <div class="cart">
            <h2>Panier</h2>
            <ul id="cart-items" class="cart-items">
                <!-- Ici seront affichés les produits ajoutés au panier -->
            </ul>
            <hr>
            <div id="total">Total: 0.00 €</div>
        </div>
    </main>
    <footer>
        <a href="index.php" class="btn">ACCUEIL</a>
        <a href="stock.php" class="btn">STOCKS</a>
        <a href="tickets.php" class="btn">TICKETS</a>
        <div class="vtbtn" onclick="validerVente()">Valider la vente</div>
    </footer>
    <script>
        var cart = [];

        function addProduct(id, name, price) {
            // Vérifier si le produit existe déjà dans le panier
            var found = false;
            for (var i = 0; i < cart.length; i++) {
                if (cart[i].id === id) {
                    cart[i].quantity++; // Mettre à jour la quantité
                    found = true;
                    break;
                }
            }
            if (!found) {
                // Ajouter le produit au panier
                cart.push({ id: id, name: name, price: price, quantity: 1 });
            }
            updateCart();
        }

        function increaseQuantity(index) {
            cart[index].quantity++;
            updateCart();
        }

        function decreaseQuantity(index) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
                updateCart();
            }
        }

        function deleteItem(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function updateCart() {
            var cartItemsUl = document.getElementById('cart-items');
            var totalDiv = document.getElementById('total');
            var total = 0;
            cartItemsUl.innerHTML = '';
            cart.forEach(function(item, index) {
                var itemLi = document.createElement('li');
                var plusButton = document.createElement('plusbutton');
                var minusButton = document.createElement('minusbutton');
                var deleteButton = document.createElement('deletebutton');

                plusButton.textContent = '+';
                minusButton.textContent = '-';
                deleteButton.textContent = 'Supprimer';

                plusButton.onclick = function() { increaseQuantity(index); };
                minusButton.onclick = function() { decreaseQuantity(index); };
                deleteButton.onclick = function() { deleteItem(index); };

                itemLi.appendChild(plusButton);
                itemLi.appendChild(minusButton);
                itemLi.appendChild(deleteButton);

                itemLi.appendChild(document.createTextNode(item.name + ' x' + item.quantity + ' - ' + (item.price * item.quantity).toFixed(2) + ' €'));

                cartItemsUl.appendChild(itemLi);
                total += parseFloat(item.price) * item.quantity;
            });
            totalDiv.textContent = 'Total: ' + total.toFixed(2) + ' €';
        }

        function validerVente() {
            var totalAmount = parseFloat(document.getElementById('total').innerText.split(':')[1].trim().split(' ')[0]);
            var cartData = JSON.stringify(cart.map(item => ({ id_produit: item.id, quantite: item.quantity, prix_unitaire: item.price })));

            $.ajax({
                type: 'POST',
                url: 'valider_vente.php',
                data: { totalAmount: totalAmount, cartData: cartData },
                success: function(response) {
                    // Redirection vers la page paiement.php après validation de la vente
                    window.location.href = 'paiement.php';
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs de la requête AJAX si nécessaire
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
</body>
</html>
