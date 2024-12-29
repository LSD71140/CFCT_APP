<?php
// Inclusion du fichier de connexion à la base de données
require_once 'db_connect.php';

// Vérifier si les données POST sont reçues
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs des montants et de l'ID de vente
    $montantCarte = isset($_POST['montantCarte']) ? $_POST['montantCarte'] : null;
    $montantEspece = isset($_POST['montantEspece']) ? $_POST['montantEspece'] : null;
    $montantCheque = isset($_POST['montantCheque']) ? $_POST['montantCheque'] : null;
    $idVente = isset($_POST['idVente']) ? $_POST['idVente'] : null;
    $manifestationSelectionnee = isset($_POST['manifestationSelectionnee']) ? $_POST['manifestationSelectionnee'] : null;
    $paiementOffert = isset($_POST['paiementOffert']) ? $_POST['paiementOffert'] : false;

    if ($paiementOffert) {
        // Logique pour enregistrer le paiement offert
        $montantCarte = 0;
        $montantEspece = 0;
        $montantCheque = 0;
    }

    // Préparer et exécuter la requête d'insertion
    $sql = "INSERT INTO Paiements (id_vente, montant_cartes, montant_especes, montant_cheque, manifestation)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iddds", $idVente, $montantCarte, $montantEspece, $montantCheque, $manifestationSelectionnee); // Utilisez 's' pour la chaîne de caractères

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Paiement enregistré avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du paiement : ' . $conn->error]);
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
}
?>
