<?php

// Fonction pour convertir le texte en format ESC/POS
function texte_to_escpos($texte) {
    // Convertir le texte en une séquence ESC/POS
    $escpos = "";
    $escpos .= "\x1B@"; // Initialisation
    $escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
	$escpos .= "\x1B\x61\x00"; // Alignement par défaut (à gauche)
	$escpos .= $texte['cfct'];	
	$escpos .= "\n"; // Saut de ligne
	$escpos .= "\x1B!\x30"; // Taille de la police : 2x hauteur, 2x largeur (plus grosse)
    $escpos .= "\x1B\x61\x01"; // Centrer le texte
	$escpos .= $texte['produit'];
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1B!\x10"; // Réinitialiser la taille de la police
    $escpos .= "\x1B\x61\x02"; // Aligner à droite
	$escpos .= $texte['manif'];
    $escpos .= "\n"; // Saut de ligne
    $escpos .= "\x1D\x56\x42\x00"; // Coupe papier (mode demi)
    
    return $escpos;
}

// Adresse IP et port de l'imprimante
$ip = '192.168.0.50';
$port = 9100; // Port par défaut pour les imprimantes réseau

// Texte à imprimer
$texte = [
    'cfct' => "Comite des fetes de Cronat\n",
	'produit' => "ANDOUILLETTE\n",
    'manif' => "Fete de l'andouille 2024"
];

// Créer une socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// Vérifier si la socket est valide
if ($socket === false) {
    echo "Erreur lors de la création de la socket : " . socket_strerror(socket_last_error());
} else {
    // Établir une connexion avec l'imprimante
    $result = socket_connect($socket, $ip, $port);
    if ($result === false) {
        echo "Erreur lors de la connexion à l'imprimante : " . socket_strerror(socket_last_error($socket));
    } else {
        // Convertir le texte en format ESC/POS
        $escpos_data = texte_to_escpos($texte);
        
        // Envoyer les données à l'imprimante
        socket_write($socket, $escpos_data, strlen($escpos_data));
        echo "Impression réussie.";
    }
    
    // Fermer la connexion
    socket_close($socket);
}
?>
