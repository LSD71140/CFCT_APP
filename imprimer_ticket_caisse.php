<?php

// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Fonction pour convertir le texte en format ESC/POS
function texte_to_escpos($texte) {
    // Convertir le texte en une séquence ESC/POS
    $escpos = "";
    $escpos .= "\x1B@"; // Initialisation
    $escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
    $escpos .= "\x1B\x61\x01"; // Centrer le texte
    $escpos .= $texte['header'];   
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1B\x61\x00"; // Alignement par défaut (à gauche)
    $escpos .= $texte['products'];
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
    $escpos .= "\x1B\x61\x02"; // Aligner à droite
    $escpos .= $texte['footer'];
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1D\x56\x42\x00"; // Coupe papier (mode demi)
    
    return $escpos;
}

// Récupérer l'ID de la vente depuis la requête POST
$id_vente = $_POST['id_vente'];

// Requête pour récupérer les détails de la vente
$query = "SELECT Ventes.prix_total, Produits.nom_produit, Produits.prix_unitaire, VenteProduits.quantite,
            Paiements.montant_cartes, Paiements.montant_especes, Paiements.montant_cheque
            FROM Ventes
            INNER JOIN VenteProduits ON Ventes.id_vente = VenteProduits.id_vente
            INNER JOIN Produits ON VenteProduits.id_produit = Produits.id_produit
            INNER JOIN Paiements ON Ventes.id_vente = Paiements.id_vente
            WHERE Ventes.id_vente = $id_vente";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
}

// Vérifier si des données ont été retournées
if (mysqli_num_rows($result) > 0) {
    // Récupérer le prix total de la vente
    $row = mysqli_fetch_assoc($result);
    $prix_total = $row['prix_total'];
    $montant_cartes = $row['montant_cartes'];
    $montant_especes = $row['montant_especes'];
    $montant_cheque = $row['montant_cheque'];
    
    // Récupérer les données de la vente
    $header = "Comite des Fetes de Cronat\nVENTE : " . $id_vente . "\n";
    $products = "";
    $total = 0;
    
    // Ajouter chaque produit vendu au texte à imprimer
    mysqli_data_seek($result, 0); // Réinitialiser le pointeur de résultat
    while ($row = mysqli_fetch_assoc($result)) {
        $products .= $row['quantite'] . " x " . $row['nom_produit'] . "  " . $row['prix_unitaire'] * $row['quantite'] . " EUR\n";
        $total += $row['prix_unitaire'] * $row['quantite'];
    }
    
    // Texte du pied de page avec le montant total et les informations de paiement
$footer = "TOTAL: " . $prix_total . " EUR\n";
if ($montant_cartes > 0) {
    $footer .= "Paye par carte: " . $montant_cartes . " EUR\n";
}
if ($montant_especes > 0) {
    $footer .= "Paye en especes: " . $montant_especes . " EUR\n";
}
if ($montant_cheque > 0) {
    $footer .= "Paye par cheque: " . $montant_cheque . " EUR\n";
}

    // Adresse IP et port de l'imprimante
    $ip = '192.168.0.50';
    $port = 9100; // Port par défaut pour les imprimantes réseau

    // Créer une socket
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // Vérifier si la socket est valide
    if ($socket === false) {
        echo "Erreur lors de la création de la socket : " . socket_strerror(socket_last_error()) . "<br>";
    } else {
        // Établir une connexion avec l'imprimante
        $connect = socket_connect($socket, $ip, $port);
        if ($connect === false) {
            echo "Erreur lors de la connexion à l'imprimante : " . socket_strerror(socket_last_error($socket)) . "<br>";
        } else {
            // Texte à imprimer
            $texte = [
                'header' => $header,
                'products' => $products,
                'footer' => $footer
            ];

            // Convertir le texte en format ESC/POS
            $escpos_data = texte_to_escpos($texte);
            
            // Envoyer les données à l'imprimante
            socket_write($socket, $escpos_data, strlen($escpos_data));
            //echo "Impression du ticket de caisse réussie.<br>";
        }
        
        // Fermer la connexion
        socket_close($socket);
    }
} else {
    echo "Aucune vente trouvée avec l'ID: " . $id_vente;
}

// Fermer la connexion à la base de données
mysqli_close($conn);
echo '<script>';
echo 'setTimeout(function() { window.location.href = "tickets.php"; }, 500);'; // Redirection après 1 seconde (1000 millisecondes)
echo '</script>';

?>
