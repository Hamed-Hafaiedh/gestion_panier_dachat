<?php
// Page produits 

session_start(); // Démarre la session PHP (pour le panier)
require 'config/connexion.php'; // Inclut le fichier de connexion PDO

// Envoie une requête SQL à PostgreSQL (seulement les produits en stock)
$stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0");
// Récupère tous les résultats sous forme de tableau PHP
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>🛒 Notre Boutique</h1>
    <!-- Compteur du panier dans le menu -->
    <a href="panier.php">
        Voir le panier 
        (<?= isset($_SESSION['panier']) ? array_sum(array_column($_SESSION['panier'], 'quantite')) : 0 ?> articles)
    </a>
</header>

<!-- Grille d'affichage des produits -->
<div class="produits-grid">
<?php foreach ($produits as $p): ?>
    <div class="carte-produit">
        <h3><?= htmlspecialchars($p['nom']) ?></h3>
        <p><?= htmlspecialchars($p['description']) ?></p>
        <p class="prix"><?= number_format($p['prix'], 2) ?> TND</p>

        <!-- Formulaire d'ajout au panier -->
        <form action="panier_action.php" method="POST">
            <!-- Champs invisibles : envoient les infos du produit au serveur -->
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="id"     value="<?= $p['id'] ?>">
            <input type="hidden" name="nom"    value="<?= htmlspecialchars($p['nom']) ?>">
            <input type="hidden" name="prix"   value="<?= $p['prix'] ?>">
            <!-- Champ visible : l'utilisateur choisit la quantité -->
            <input type="number" name="quantite" value="1" min="1" max="<?= $p['stock'] ?>">
            <button type="submit">Ajouter au panier</button>
        </form>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>