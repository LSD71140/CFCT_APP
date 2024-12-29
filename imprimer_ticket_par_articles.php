<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Fonction pour convertir le texte en format ESC/POS
function texte_to_escpos($texte) {
    // Convertir le texte en une séquence ESC/POS
    $escpos = "";
    $escpos .= "\x1B@"; // Initialisation
    $escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
    $escpos .= "\x1B\x61\x00"; // Alignement par défaut (à gauche)
    $escpos .= $texte['cfct'];   
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1D!\x21\x3"; // Taille de la police : 2x hauteur, 2x largeur (plus grosse)
    $escpos .= "\x1B\x61\x01"; // Centrer le texte
	//$escpos .= "\x1B\x49"; // Activer le mode gras
    $escpos .= $texte['produit'];
    $escpos .= "\n"; // Saut de ligne
   	//$escpos .= "\x1B\x48"; // Désactiver le mode gras
	$escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
    $escpos .= "\x1B\x61\x02"; // Aligner à droite
    $escpos .= $texte['manif'];
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1D\x56\x42\x00"; // Coupe papier (mode demi)
    
    return $escpos;
}

// Récupérer l'ID de la vente depuis la requête POST
$id_vente = $_POST['id_vente'];
echo "ID de la vente : " . $id_vente . "<br>";

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
        // Requête pour récupérer les produits vendus dans la vente avec leur quantité et la manifestation associée
        $sql = "SELECT Produits.nom_produit, VenteProduits.quantite, Paiements.manifestation
                FROM VenteProduits
                INNER JOIN Produits ON VenteProduits.id_produit = Produits.id_produit
                INNER JOIN Paiements ON VenteProduits.id_vente = Paiements.id_vente
                WHERE VenteProduits.id_vente = $id_vente";
        echo "Requête SQL : " . $sql . "<br>";

        // Exécution de la requête
        $result = $conn->query($sql);

        // Vérifier si la requête a réussi
        if ($result === false) {
            echo "Erreur lors de l'exécution de la requête : " . $conn->error . "<br>";
        } else {
            // Vérifier si des produits ont été vendus dans cette vente
            if ($result->num_rows > 0) {
                // Pour chaque produit vendu, imprimer un ticket pour chaque exemplaire vendu
                while ($row = $result->fetch_assoc()) {
                    $produit = $row['nom_produit'];
                    $quantite = $row['quantite'];
                    $manifestation = $row['manifestation'];
                    
                    // Nom de la manifestation en fonction du choix
                    switch ($manifestation) {
                        case '1ermai-midi':
                        case '1ermai-soir':
                            $nom_manifestation = "Fete de l'andouille " . date('Y');
                            break;
                        case '13juillet':
                            $nom_manifestation = "13 JUILLET " . date('Y');
                            break;
                        case 'patronale':
                            $nom_manifestation = "Fete Patronale " . date('Y');
                            break;
                        default:
                            $nom_manifestation = "Manifestation inconnue";
                            break;
                    }
                    
                    // Imprimer autant de tickets que la quantité vendue
                    for ($i = 0; $i < $quantite; $i++) {
                        // Texte à imprimer pour ce produit
                        $texte = [
                            'cfct' => "Comite des fetes de Cronat / VENTE : " . $id_vente . "\n",
                            'produit' => $produit, // Nom du produit
                            'manif' => $nom_manifestation // Nom de la manifestation
                        ];

                        // Convertir le texte en format ESC/POS
                        $escpos_data = texte_to_escpos($texte);
                        
                        // Envoyer les données à l'imprimante
                        $bytes_written = socket_write($socket, $escpos_data, strlen($escpos_data));
                        if ($bytes_written === false) {
                            echo "Erreur lors de l'écriture des données à l'imprimante : " . socket_strerror(socket_last_error($socket)) . "<br>";
                        } else {
                            echo "Impression réussie pour " . $produit . ".<br>";
                        }
                    }
                }
            } else {
                echo "Aucun produit vendu dans cette vente.<br>";
            }
        }
    }
    
    // Fermer la connexion à la socket
    socket_close($socket);
}

// Fermer la connexion à la base de données
$conn->close();
echo '<script>';
echo 'setTimeout(function() { window.location.href = "tickets.php"; }, 500);'; // Redirection après 1 seconde (1000 millisecondes)
echo '</script>';
?>
