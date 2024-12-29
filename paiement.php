<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link rel="stylesheet" href="paiement.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>
    <h1>Paiement</h1>
    <div id="id_vente" data-id-vente="<?php echo $id_vente; ?>"></div> <!-- Ajout de l'attribut de données personnalisé pour stocker l'ID de vente -->
    
    <div id="options">
        <div id="manifestation-select">
            <label for="pet-select">MANIFESTATION:</label>
            <select name="pets" id="pet-select">
                <option value="">--Merci de choisir une option--</option>
                <option value="1ermai-midi">1er MAI MIDI</option>
                <option value="1ermai-soir">1er MAI SOIR</option>
                <option value="13juillet">13 JUILLET</option>
                <option value="patronale">Fête Patronale</option>
            </select>
        </div>
        <button id="offert">GRATUIT</button>
        <button id="carte">Carte</button>
        <input type="number" step="0.01" id="montant_carte" placeholder="Montant pour Carte">
        <button id="especes">Espèces</button>
        <input type="number" id="montant_especes" placeholder="Montant pour Espèces">
        <button id="cheque">Chèque</button>
        <input type="number" id="montant_cheque" placeholder="Montant pour Chèque">
        <div id="total">TOTAL: <span id="montant_total">0.00 €</span></div>
        <div id="reste_a_payer">Reste à payer: <span id="reste">0.00 €</span></div>
        <button id="valider_paiement">Valider le paiement</button>
    </div>
    <footer>
        <a href="index.php" class="btn">ACCUEIL</a>
        <a href="javascript:history.go(-1)" class="btn">CAISSE</a>
        <a href="stock.php" class="btn">STOCKS</a>
    </footer>
    <script>
        // Récupérer la valeur sélectionnée dans la liste déroulante depuis le stockage local
        $(document).ready(function() {
            var selectedManifestation = localStorage.getItem('selectedManifestation');
            if (selectedManifestation) {
                $('#pet-select').val(selectedManifestation); // Utilisez #pet-select au lieu de #manifestation-select
            }
        });

        // Sauvegarder la valeur sélectionnée dans la liste déroulante dans le stockage local
        $('#pet-select').change(function() {
            var selectedManifestation = $(this).val();
            localStorage.setItem('selectedManifestation', selectedManifestation);
        });

        // Fonction pour récupérer le montant total et l'ID de vente depuis la base de données
        $(document).ready(function() {
            $.ajax({
                type: 'GET',
                url: 'get_total_amount.php',
                dataType: 'json', // Indique que la réponse est au format JSON
                success: function(response) {
                    var montantTotal = parseFloat(response.prix_total); // Utilisez la clé 'prix_total' pour récupérer le montant total
                    var idVente = response.id_vente;
                    document.getElementById('montant_total').textContent = montantTotal.toFixed(2) + ' €';
                    document.getElementById('id_vente').setAttribute('data-id-vente', idVente); // Stocke l'ID de vente dans l'attribut de données personnalisé
                    calculerReste();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Fonction pour calculer le reste à payer en temps réel
        function calculerReste() {
            var montantTotal = parseFloat(document.getElementById('montant_total').textContent);
            var montantCarte = parseFloat(document.getElementById('montant_carte').value) || 0;
            var montantEspece = parseFloat(document.getElementById('montant_especes').value) || 0;
            var montantCheque = parseFloat(document.getElementById('montant_cheque').value) || 0;

            var montantTotalPaye = montantCarte + montantEspece + montantCheque;
            var reste = montantTotal - montantTotalPaye;

            document.getElementById('reste').textContent = reste.toFixed(2) + ' €';
        }

        // Écouter les événements de changement d'entrée pour recalculer le reste à payer
        var inputs = document.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            input.addEventListener('input', calculerReste);
            input.addEventListener('change', calculerReste); // Ajout de l'écouteur pour le cas où le contenu est effacé
        });

        // Écouter les clics sur les boutons pour mettre à jour les montants correspondants
        document.getElementById('carte').addEventListener('click', function() {
            var resteAPayer = parseFloat(document.getElementById('reste').textContent);
            document.getElementById('montant_carte').value = resteAPayer.toFixed(2);
            calculerReste();
        });

        document.getElementById('especes').addEventListener('click', function() {
            var resteAPayer = parseFloat(document.getElementById('reste').textContent);
            document.getElementById('montant_especes').value = resteAPayer.toFixed(2);
            calculerReste();
        });

        document.getElementById('cheque').addEventListener('click', function() {
            var resteAPayer = parseFloat(document.getElementById('reste').textContent);
            document.getElementById('montant_cheque').value = resteAPayer.toFixed(2);
            calculerReste();
        });

        // Ajouter l'événement de clic pour le bouton "offert"
        document.getElementById('offert').addEventListener('click', function() {
            validerPaiementOffert();
        });

        function validerPaiementOffert() {
            var idVente = document.getElementById('id_vente').getAttribute('data-id-vente');
            var manifestationSelectionnee = document.getElementById('pet-select').value;

            $.ajax({
                type: 'POST',
                url: 'enregistrer_paiement.php',
                data: {
                    montantCarte: 0,
                    montantEspece: 0,
                    montantCheque: 0,
                    idVente: idVente,
                    manifestationSelectionnee: manifestationSelectionnee,
                    paiementOffert: true // Indique que le paiement est offert
                },
                success: function(response) {
                    alert('Paiement enregistré comme offert ! Appuyer sur OK pour imprimer les tickets');
                    window.location.href = 'caisse.php'; // Rediriger vers la page caisse.php une fois le message de paiement enregistré fermé

                    // Appeler print_ticket.php pour imprimer le ticket
                    $.ajax({
                        type: 'POST',
                        url: 'print_ticket.php',
                        data: {
                            id_vente: idVente,
                            manifestationSelectionnee: manifestationSelectionnee
                        },
                        success: function(response) {
                            // Gérer la réponse ou les actions à effectuer après l'impression du ticket
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert('Erreur lors de l\'impression du ticket');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Erreur lors de l\'enregistrement du paiement offert');
                }
            });
        }

        // Écouter l'événement de clic sur le bouton de validation
        document.getElementById('valider_paiement').addEventListener('click', function() {
            var montantCarte = parseFloat(document.getElementById('montant_carte').value) || 0;
            var montantEspece = parseFloat(document.getElementById('montant_especes').value) || 0;
            var montantCheque = parseFloat(document.getElementById('montant_cheque').value) || 0;
            var idVente = document.getElementById('id_vente').getAttribute('data-id-vente'); // Récupère l'ID de vente depuis l'attribut de données personnalisé
            var manifestationSelectionnee = document.getElementById('pet-select').value; // Récupère la manifestation sélectionnée

            // Vérifier si au moins un montant est non nul
            if (montantCarte > 0 || montantEspece > 0 || montantCheque > 0) {
                // Envoyer les données au serveur pour l'enregistrement
                $.ajax({
                    type: 'POST',
                    url: 'enregistrer_paiement.php',
                    data: {
                        montantCarte: montantCarte,
                        montantEspece: montantEspece,
                        montantCheque: montantCheque,
                        idVente: idVente,
                        manifestationSelectionnee: manifestationSelectionnee // Inclure la manifestation sélectionnée dans les données envoyées
                    },
                    success: function(response) {
                        alert('Paiement enregistré ! Appuyer sur OK pour imprimer les tickets');
                        window.location.href = 'caisse.php'; // Rediriger vers la page caisse.php une fois le message de paiement enregistré fermé

                        // Appeler print_ticket.php pour imprimer le ticket
                        $.ajax({
                            type: 'POST', // Changer la méthode en POST
                            url: 'print_ticket.php',
                            data: { // Envoyer les données nécessaires via POST
                                id_vente: idVente,
                                manifestationSelectionnee: manifestationSelectionnee
                            },
                            success: function(response) {
                                // Gérer la réponse ou les actions à effectuer après l'impression du ticket
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                alert('Erreur lors de l\'impression du ticket');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Erreur lors de l\'enregistrement du paiement');
                    }
                });
            } else {
                alert('Veuillez saisir au moins un montant.');
            }
        });
    </script>
</body>
</html>
