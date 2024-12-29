<div id="panache-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePanacheModal()">&times;</span>
        <h2>Paiement Panaché</h2>
        <form id="panache-form" action="process_panache_payment.php" method="post">
            <label for="first-payment-method">Premier moyen de paiement:</label>
            <select id="first-payment-method" name="first_payment_method">
                <option value="carte">Carte</option>
                <option value="especes">Espèces</option>
                <option value="cheque">Chèque</option>
            </select>
            <br>
            <label for="first-amount">Montant du premier paiement (en euros):</label>
            <input type="number" step="0.01" id="first-amount" name="first_amount" placeholder="Montant en euros" onchange="calculateSecondAmount()">
            <br>
            <label for="second-payment-method">Deuxième moyen de paiement:</label>
            <select id="second-payment-method" name="second_payment_method">
                <option value="carte">Carte</option>
                <option value="especes">Espèces</option>
                <option value="cheque">Chèque</option>
            </select>
            <br>
            <label for="second-amount">Montant du deuxième paiement (en euros):</label>
            <input type="number" step="0.01" id="second-amount" name="second_amount" placeholder="Montant en euros">
            <br>
            <input type="hidden" id="cart-data" name="cart_data">
            <input type="submit" value="Valider">
        </form>
    </div>
</div>

<script>
    // Fonction pour fermer la fenêtre modale pour le paiement panaché
    function closePanacheModal() {
        var modal = document.getElementById('panache-modal');
        modal.style.display = 'none';
    }

    // Fonction pour ouvrir la fenêtre modale pour le paiement panaché
    function openPanacheModal() {
        var modal = document.getElementById('panache-modal');
        modal.style.display = 'block';
    }

    // Fonction pour calculer automatiquement le montant restant à payer dans le deuxième montant
    function calculateSecondAmount() {
        var firstAmount = parseFloat(document.getElementById('first-amount').value);
        var totalAmount = parseFloat(document.getElementById('total').innerText.split(':')[1].trim().split(' ')[0]); // Récupération du total depuis l'élément avec l'ID 'total'
        var secondAmount = totalAmount - firstAmount;
        document.getElementById('second-amount').value = secondAmount.toFixed(2);
    }

    // Fonction pour préparer les données du panier à envoyer avec le formulaire
    function prepareCartData() {
        var cartData = JSON.stringify(cart);
        document.getElementById('cart-data').value = cartData;
    }
</script>
