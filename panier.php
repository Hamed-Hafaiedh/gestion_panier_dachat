<?php
// ============================================
// PAGE PANIER - Affiche le contenu du panier
// ============================================

session_start();   // Démarre la session pour accéder au panier

// Récupère le panier (tableau vide si pas encore de panier)
$panier = $_SESSION['panier'] ?? [];

// Calcule le total général
$total = 0;
foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ============================================
     HEADER
     ============================================ -->
<header>
    <h1>🛍️ Mon Panier</h1>
    <a href="index.php">← Continuer les achats</a>
</header>

<div class="conteneur-panier">

<?php if (empty($panier)): ?>
    <!-- ========================================
         PANIER VIDE
         ======================================== -->
    <div class="panier-vide">
        <div class="icone">🛒</div>
        <p>Votre panier est vide.</p>
        <a href="index.php">Voir les produits</a>
    </div>

<?php else: ?>
    <!-- ========================================
         TABLEAU DES PRODUITS
         ======================================== -->
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($panier as $id => $item):
            $sous_total = $item['prix'] * $item['quantite'];
        ?>
            <tr>
                <!-- Nom du produit -->
                <td><strong><?= htmlspecialchars($item['nom']) ?></strong></td>

                <!-- Prix unitaire -->
                <td><?= number_format($item['prix'], 2) ?> TND</td>

                <!-- Formulaire de modification de quantité -->
                <td>
                    <form action="panier_action.php" method="POST"
                          onsubmit="return validerModification(this)"
                          style="flex-direction:row; align-items:center;">

                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="form-ligne">
                            <input type="number"
                                   name="quantite"
                                   value="<?= $item['quantite'] ?>"
                                   min="0"
                                   id="qte_mod_<?= $id ?>">
                            <button type="submit" class="btn-modifier">✔</button>
                        </div>
                        <span class="erreur-message" id="err_mod_<?= $id ?>"></span>
                    </form>
                </td>

                <!-- Sous-total -->
                <td><strong><?= number_format($sous_total, 2) ?> TND</strong></td>

                <!-- Bouton supprimer -->
                <td>
                    <form action="panier_action.php" method="POST">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="btn-supprimer">🗑️</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <!-- Total général -->
    <div class="total">
        Total : <span><?= number_format($total, 2) ?> TND</span>
    </div>

    <!-- Bouton valider la commande -->
    <form action="valider_commande.php" method="POST">
        <button type="submit" class="btn-valider">✅ Valider la commande</button>
    </form>

<?php endif; ?>

</div><!-- fin conteneur-panier -->

<!-- ============================================
     JAVASCRIPT - Contrôle de saisie
     ============================================ -->
<script>
/**
 * Valide la modification de quantité
 * Quantité = 0 → supprime le produit (c'est voulu)
 */
function validerModification(form) {
    const inputQte = form.querySelector('input[name="quantite"]');
    const id = form.querySelector('input[name="id"]').value;
    const erreur = document.getElementById('err_mod_' + id);
    const qte = parseInt(inputQte.value);

    // Vérifie que c'est bien un nombre
    if (isNaN(qte) || qte < 0) {
        erreur.textContent = '❌ Quantité invalide (minimum 0).';
        erreur.style.display = 'block';
        inputQte.focus();
        return false;
    }

    // Si quantité = 0, demande confirmation avant de supprimer
    if (qte === 0) {
        return confirm('Mettre la quantité à 0 supprimera ce produit. Confirmer ?');
    }

    erreur.style.display = 'none';
    return true;
}
</script>

</body>
</html>