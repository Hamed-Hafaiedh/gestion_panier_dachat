<?php
// Démarrer la session pour accéder au panier
session_start();

// Inclure la connexion à la base de données (PDO)
require 'config/connexion.php';

// Vérifier si le panier est vide
if (empty($_SESSION['panier'])) {

    // Si vide → redirection vers la boutique
    header('Location: index.php');
    exit;
}

// Récupérer le panier depuis la session
$panier = $_SESSION['panier'];

// Initialiser le total
$total = 0;

// Calculer le total de la commande
foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}

try {
    // Démarrer une transaction (important pour la cohérence des données)
    // garantit que tout se passe bien (commande + détails)
    $pdo->beginTransaction();

    // =========================
    // 1. INSÉRER LA COMMANDE
    // =========================

    // Préparer la requête SQL
    // On insère le total et un statut "validée"
    // RETURNING id permet de récupérer l'id de la commande créée (PostgreSQL)
    $stmt = $pdo->prepare("INSERT INTO commandes (total, statut) VALUES (?, 'validée') RETURNING id");

    // Exécuter la requête avec le total
    $stmt->execute([$total]);

    // Récupérer l'id de la commande créée
    $commande_id = $stmt->fetchColumn();

    // =========================
    // 2. INSÉRER LES DÉTAILS
    // =========================

    // Préparer la requête pour les produits
    $stmt2 = $pdo->prepare("
        INSERT INTO commande_details 
        (commande_id, produit_id, quantite, prix_unitaire) 
        VALUES (?, ?, ?, ?)
    ");

    // Parcourir chaque produit du panier
    foreach ($panier as $produit_id => $item) {

        // Insérer chaque produit dans la table commande_details
        $stmt2->execute([
            $commande_id,           // id de la commande
            $produit_id,            // id du produit
            $item['quantite'],      // quantité
            $item['prix']           // prix unitaire
        ]);
    }

    // =========================
    // 3. VALIDER LA TRANSACTION
    // =========================

    $pdo->commit();

    // =========================
    // 4. VIDER LE PANIER
    // =========================

    unset($_SESSION['panier']);

    // =========================
    // 5. MESSAGE DE SUCCÈS
    // =========================

    echo "<h2> Commande #$commande_id validée avec succès !</h2>";
    echo "<p>Total : " . number_format($total, 2) . " TND</p>";
    echo '<a href="index.php">Retour à la boutique</a>';

} catch (Exception $e) {

    // En cas d'erreur → annuler toutes les opérations
    $pdo->rollBack();

    // Afficher l'erreur
    echo "Erreur : " . $e->getMessage();
}
?>