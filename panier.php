<?php
// ============================================
// PAGE PANIER - Affiche et gère le panier
// ============================================

session_start();

// ============================================
// TRAITEMENT DU FORMULAIRE
// Ce bloc s'exécute quand on clique "Mettre à jour" ou "Supprimer"
// ============================================

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $id     = (int)$_POST['id'];

    // ---- CAS 1 : MODIFIER LA QUANTITÉ ----
    if ($action === 'modifier') {

        $qte = (int)$_POST['quantite'];

        if ($qte < 0) {
            // Quantité négative → erreur
            $erreur = "❌ La quantité ne peut pas être négative.";

        } elseif ($qte === 0) {
            // Quantité = 0 → on supprime le produit
            unset($_SESSION['panier'][$id]);
            header('Location: panier.php');
            exit;

        } else {
            // ✅ Quantité valide → on met à jour
            $_SESSION['panier'][$id]['quantite'] = $qte;
            header('Location: panier.php');
            exit;
        }
    }

    // ---- CAS 2 : SUPPRIMER UN PRODUIT ----
    if ($action === 'supprimer') {
        unset($_SESSION['panier'][$id]);
        header('Location: panier.php');
        exit;
    }
}

// ============================================
// RÉCUPÉRATION DU PANIER ET CALCUL DU TOTAL
// ============================================

$panier = $_SESSION['panier'] ?? [];
$total  = 0;

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

<header>
    <h1>🛍️ Mon Panier</h1>
    <a href="index.php">← Continuer les achats</a>
</header>

<div class="conteneur-panier">

<?php if ($erreur !== ''): ?>
    <!-- Message d'erreur -->
    <p class="message-erreur"><?= $erreur ?></p>
<?php endif; ?>

<?php if (empty($panier)): ?>

    <!-- Panier vide -->
    <div class="panier-vide">
        <div class="icone">🛒</div>
        <p>Votre panier est vide.</p>
        <a href="index.php">Voir les produits</a>
    </div>

<?php else: ?>

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
                <td><strong><?= htmlspecialchars($item['nom']) ?></strong></td>
                <td><?= number_format($item['prix'], 2) ?> TND</td>

                <!-- Formulaire modifier quantité -->
                <td>
                    <form action="panier.php" method="POST" style="flex-direction:row;">
                        <input type="hidden" name="action"   value="modifier">
                        <input type="hidden" name="id"       value="<?= $id ?>">
                        <div class="form-ligne">
                            <input type="number" name="quantite"
                                   value="<?= $item['quantite'] ?>" min="0">
                            <button type="submit" class="btn-modifier">✔</button>
                        </div>
                    </form>
                </td>

                <td><strong><?= number_format($sous_total, 2) ?> TND</strong></td>

                <!-- Formulaire supprimer -->
                <td>
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id"     value="<?= $id ?>">
                        <button type="submit" class="btn-supprimer">🗑️</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <div class="total">
        Total : <span><?= number_format($total, 2) ?> TND</span>
    </div>

    <form action="valider_commande.php" method="POST">
        <button type="submit" class="btn-valider">✅ Valider la commande</button>
    </form>

<?php endif; ?>

</div>

</body>
</html>