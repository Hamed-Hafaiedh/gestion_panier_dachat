<?php
// Démarrer la session pour accéder au panier stocké
session_start();

// Récupérer le panier depuis la session
// Si le panier n'existe pas, on met un tableau vide
$panier = $_SESSION['panier'] ?? [];

// Initialiser le total à 0
$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Mon Panier</h1>

    <!-- Lien pour revenir à la page des produits -->
    <a href="index.php">← Continuer les achats</a>
</header>

<?php if (empty($panier)): ?>
    <!-- Si le panier est vide -->
    <p>Votre panier est vide.</p>

<?php else: ?>

<!-- Tableau qui affiche les produits -->
<table>
    <thead>
        <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Sous-total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    <?php 
    // Parcourir chaque produit dans le panier
    foreach ($panier as $id => $item): 
        
        // Calcul du sous-total pour chaque produit
        $sous_total = $item['prix'] * $item['quantite'];

        // Ajouter au total général
        $total += $sous_total;
    ?>

        <tr>
            <!-- Afficher le nom du produit (sécurisé contre XSS) -->
            <td><?= htmlspecialchars($item['nom']) ?></td>

            <!-- Afficher le prix avec 2 chiffres après la virgule -->
            <td><?= number_format($item['prix'], 2) ?> TND</td>

            <td>
                <!-- Formulaire pour modifier la quantité -->
                <form action="panier_action.php" method="POST">

                    <!-- Action = modifier -->
                    <input type="hidden" name="action" value="modifier">

                    <!-- ID du produit -->
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <!-- Champ pour changer la quantité -->
                    <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="0">

                    <!-- Bouton de mise à jour -->
                    <button type="submit">Mettre à jour</button>
                </form>
            </td>

            <!-- Afficher le sous-total -->
            <td><?= number_format($sous_total, 2) ?> TND</td>

            <td>
                <!-- Formulaire pour supprimer le produit -->
                <form action="panier_crud.php" method="POST">

                    <!-- Action = supprimer -->
                    <input type="hidden" name="action" value="supprimer">

                    <!-- ID du produit -->
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <!-- Bouton supprimer -->
                    <button type="submit" class="btn-supprimer">Supprimer</button>
                </form>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>
</table>

<!-- Affichage du total -->
<div class="total">
    <strong>Total : <?= number_format($total, 2) ?> TND</strong>
</div>

<!-- Formulaire pour valider la commande -->
<form action="valider_commande.php" method="POST">
    <button type="submit" class="btn-valider">Valider la commande</button>
</form>

<?php endif; ?>

</body>
</html>