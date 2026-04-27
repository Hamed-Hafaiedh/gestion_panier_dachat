<?php
// ============================================
// PAGE PRINCIPALE - Affichage des produits
// ============================================

session_start();
require 'config/connexion.php';

// ============================================
// TRAITEMENT DU FORMULAIRE
// Ce bloc s'exécute seulement quand on clique "Ajouter"
// ============================================

$erreur = '';   // message d'erreur (vide au départ)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère et sécurise les données du formulaire
    $id        = (int)$_POST['id'];
    $nom       = htmlspecialchars($_POST['nom']);
    $prix      = (float)$_POST['prix'];
    $qte       = (int)$_POST['quantite'];
    $stock_max = (int)$_POST['stock_max'];

    // ---- CONTRÔLE DE SAISIE PHP ----

    if ($qte < 1) {
        // Quantité trop basse
        $erreur = "❌ La quantité doit être au moins 1.";

    } elseif ($qte > $stock_max) {
        // Quantité dépasse le stock disponible
        $erreur = "❌ Stock insuffisant. Maximum disponible : $stock_max.";

    } else {
        // ✅ Données valides → on ajoute au panier

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        if (isset($_SESSION['panier'][$id])) {
            // Produit déjà dans le panier → on ajoute la quantité
            $_SESSION['panier'][$id]['quantite'] += $qte;
        } else {
            // Nouveau produit → on crée l'entrée
            $_SESSION['panier'][$id] = [
                'nom'      => $nom,
                'prix'     => $prix,
                'quantite' => $qte
            ];
        }

        // Redirige pour éviter le re-envoi si on recharge la page
        header('Location: index.php');
        exit;
    }
}

// ============================================
// RÉCUPÉRATION DES PRODUITS depuis la base
// ============================================

$stmt     = $pdo->query("SELECT * FROM produits WHERE stock > 0");
$produits = $stmt->fetchAll();

// Compte les articles dans le panier (pour le header)
$nb_articles = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $nb_articles += $item['quantite'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 Notre Boutique</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>🛒 Notre Boutique</h1>
    <a href="panier.php">
        🛍️ Panier (<?= $nb_articles ?> article<?= $nb_articles > 1 ? 's' : '' ?>)
    </a>
</header>

<?php if ($erreur !== ''): ?>
    <!-- Affiche le message d'erreur en rouge si il y en a un -->
    <p class="message-erreur"><?= $erreur ?></p>
<?php endif; ?>

<h2 class="titre-section">Nos Produits</h2>

<div class="produits-grid">
<?php foreach ($produits as $p): ?>

    <div class="carte-produit">
        <h3><?= htmlspecialchars($p['nom']) ?></h3>
        <p><?= htmlspecialchars($p['description']) ?></p>
        <p class="prix"><?= number_format($p['prix'], 2) ?> TND</p>
        <small style="color:#999;">Stock : <?= $p['stock'] ?> restant(s)</small>

        <!-- Le formulaire envoie vers index.php (cette même page) -->
        <form action="index.php" method="POST">

            <!-- Champs cachés : infos du produit envoyées au serveur -->
            <input type="hidden" name="id"        value="<?= $p['id'] ?>">
            <input type="hidden" name="nom"       value="<?= htmlspecialchars($p['nom']) ?>">
            <input type="hidden" name="prix"      value="<?= $p['prix'] ?>">
            <input type="hidden" name="stock_max" value="<?= $p['stock'] ?>">

            <div class="form-ligne">
                <label>Qté :</label>
                <input type="number" name="quantite" value="1" min="1">
            </div>

            <button type="submit" class="btn-ajouter">🛒 Ajouter au panier</button>
        </form>
    </div>

<?php endforeach; ?>
</div>

</body>
</html>