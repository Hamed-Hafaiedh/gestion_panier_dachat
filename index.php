<?php
// ============================================
// PAGE PRINCIPALE - Affichage des produits
// ============================================

session_start();                   // Démarre la session (nécessaire pour le panier)
require 'config/connexion.php';    // Connexion à la base de données PostgreSQL

// Récupère tous les produits qui ont du stock disponible
$stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compte le total d'articles dans le panier (pour afficher dans le header)
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

<!-- ============================================
     HEADER - Barre du haut
     ============================================ -->
<header>
    <h1>Bienvenue</h1>
    <h1>🛒 Notre Boutique</h1>
    <a href="panier.php">
        🛍️ Panier (<?= $nb_articles ?> article<?= $nb_articles > 1 ? 's' : '' ?>)
    </a>
</header>

<h2 class="titre-section">Nos Produits</h2>

<!-- ============================================
     GRILLE DES PRODUITS
     ============================================ -->
<div class="produits-grid">

    <?php if (empty($produits)): ?>
        <p style="text-align:center; color:#777;">Aucun produit disponible pour le moment.</p>

    <?php else: ?>

        <?php foreach ($produits as $p): ?>
        <div class="carte-produit">

            <!-- Nom du produit (htmlspecialchars évite les injections HTML) -->
            <h3><?= htmlspecialchars($p['nom']) ?></h3>

            <!-- Description -->
            <p><?= htmlspecialchars($p['description']) ?></p>

            <!-- Prix -->
            <p class="prix"><?= number_format($p['prix'], 2) ?> TND</p>

            <!-- Stock restant -->
            <small style="color:#999;">Stock : <?= $p['stock'] ?> restant(s)</small>

            <!-- ========================================
                 FORMULAIRE D'AJOUT AU PANIER
                 avec contrôle de saisie JavaScript
                 ======================================== -->
            <form action="panier_action.php" method="POST"
                  onsubmit="return validerAjout(this, <?= $p['stock'] ?>)">

                <!-- Champs cachés : envoient les infos au serveur -->
                <input type="hidden" name="action" value="ajouter">
                <input type="hidden" name="id"     value="<?= $p['id'] ?>">
                <input type="hidden" name="nom"    value="<?= htmlspecialchars($p['nom']) ?>">
                <input type="hidden" name="prix"   value="<?= $p['prix'] ?>">

                <!-- Champ quantité visible -->
                <div class="form-ligne">
                    <label>Qté :</label>
                    <input type="number"
                           name="quantite"
                           value="1"
                           min="1"
                           max="<?= $p['stock'] ?>"
                           id="qte_<?= $p['id'] ?>">
                </div>

                <!-- Message d'erreur (caché par défaut, affiché par JS) -->
                <span class="erreur-message" id="err_<?= $p['id'] ?>"></span>

                <button type="submit" class="btn-ajouter">🛒 Ajouter au panier</button>
            </form>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<!-- ============================================
     JAVASCRIPT - Contrôle de saisie côté client
     ============================================ -->
<script>
/**
 * Valide la quantité avant l'envoi du formulaire
 * @param {HTMLFormElement} form - Le formulaire soumis
 * @param {number} stockMax - Le stock maximum disponible
 * @returns {boolean} - true = envoyer, false = bloquer
 */
function validerAjout(form, stockMax) {
    // Récupère le champ quantité du formulaire
    const inputQte = form.querySelector('input[name="quantite"]');
    const qte = parseInt(inputQte.value);

    // Récupère l'id du produit pour afficher l'erreur au bon endroit
    const id = form.querySelector('input[name="id"]').value;
    const erreur = document.getElementById('err_' + id);

    // Vérifie que la quantité est un nombre valide
    if (isNaN(qte) || qte < 1) {
        erreur.textContent = '❌ La quantité doit être au moins 1.';
        erreur.style.display = 'block';
        inputQte.focus();   // Met le focus sur le champ
        return false;       // Bloque l'envoi
    }

    // Vérifie que la quantité ne dépasse pas le stock
    if (qte > stockMax) {
        erreur.textContent = `❌ Stock insuffisant. Maximum : ${stockMax}`;
        erreur.style.display = 'block';
        inputQte.focus();
        return false;
    }

    // Tout est bon : cache l'erreur et envoie le formulaire
    erreur.style.display = 'none';
    return true;
}
</script>

</body>
</html>