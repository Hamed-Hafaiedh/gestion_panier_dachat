<?php
// ============================================
// VALIDER COMMANDE - Enregistre la commande
// dans la base de données PostgreSQL
// ============================================

session_start();
require 'config/connexion.php';

// ============================================
// VÉRIFICATION : Panier non vide
// ============================================
if (empty($_SESSION['panier'])) {
    // Si le panier est vide, retour à la boutique
    header('Location: index.php');
    exit;
}

// Récupère le panier et calcule le total
$panier = $_SESSION['panier'];
$total  = 0;

foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}

// ============================================
// ENREGISTREMENT EN BASE DE DONNÉES
// On utilise une transaction pour garantir
// que tout s'enregistre ou rien (cohérence)
// ============================================
try {

    // Démarre la transaction
    $pdo->beginTransaction();

    // --- ÉTAPE 1 : Créer la commande principale ---
    // INSERT retourne l'id créé grâce à RETURNING (spécifique à PostgreSQL)
    $stmt = $pdo->prepare("
        INSERT INTO commandes (total, statut)
        VALUES (?, 'validée')
        RETURNING id
    ");
    $stmt->execute([$total]);
    $commande_id = $stmt->fetchColumn();   // récupère l'id généré

    // --- ÉTAPE 2 : Enregistrer chaque produit du panier ---
    $stmt2 = $pdo->prepare("
        INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($panier as $produit_id => $item) {
        $stmt2->execute([
            $commande_id,
            $produit_id,
            $item['quantite'],
            $item['prix']
        ]);
    }

    // --- ÉTAPE 3 : Valider la transaction ---
    $pdo->commit();

    // --- ÉTAPE 4 : Vider le panier ---
    unset($_SESSION['panier']);

    // ============================================
    // AFFICHAGE PAGE DE SUCCÈS
    // ============================================
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Commande confirmée</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <header>
        <h1>🛒 Notre Boutique</h1>
        <a href="index.php">← Retour à la boutique</a>
    </header>

    <div class="confirmation">
        <div class="icone-succes">✅</div>
        <h2>Commande confirmée !</h2>
        <p>
            Commande <strong>#<?= $commande_id ?></strong> enregistrée avec succès.<br>
            Montant total : <strong><?= number_format($total, 2) ?> TND</strong>
        </p>
        <a href="index.php">Retour à la boutique</a>
    </div>
    </body>
    </html>

    <?php

} catch (Exception $e) {

    // En cas d'erreur → annule toutes les insertions
    $pdo->rollBack();

    echo "<p style='color:red; padding:20px;'>
            ❌ Une erreur s'est produite : " . htmlspecialchars($e->getMessage()) . "
          </p>";
    echo '<a href="panier.php">← Retour au panier</a>';
}
?>