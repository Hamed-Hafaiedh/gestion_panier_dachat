<?php
// ============================================
// PANIER ACTION - Traite les actions du panier
// (ajouter, modifier, supprimer)
// ============================================

session_start();   // Nécessaire pour accéder à $_SESSION

// Récupère l'action envoyée par le formulaire
$action = $_POST['action'] ?? '';

// ============================================
// SÉCURITÉ : Vérifie que la requête est POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// ============================================
// CAS 1 : AJOUTER UN PRODUIT AU PANIER
// ============================================
if ($action === 'ajouter') {

    // Récupère et nettoie les données du formulaire
    $id   = (int)$_POST['id'];              // converti en entier (sécurité)
    $nom  = htmlspecialchars($_POST['nom']); // protège contre les injections HTML
    $prix = (float)$_POST['prix'];           // converti en nombre décimal
    $qte  = (int)$_POST['quantite'];         // converti en entier

    // CONTRÔLE CÔTÉ SERVEUR : vérifie que les données sont valides
    if ($id <= 0 || $qte < 1 || $prix <= 0) {
        // Données invalides → on redirige sans rien faire
        header('Location: index.php');
        exit;
    }

    // Initialise le panier si il n'existe pas encore
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Si le produit est déjà dans le panier → ajoute la quantité
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]['quantite'] += $qte;
    } else {
        // Sinon → crée une nouvelle entrée dans le panier
        $_SESSION['panier'][$id] = [
            'nom'      => $nom,
            'prix'     => $prix,
            'quantite' => $qte
        ];
    }

    // Redirige vers la boutique après l'ajout
    header('Location: index.php');
    exit;
}

// ============================================
// CAS 2 : MODIFIER LA QUANTITÉ
// ============================================
if ($action === 'modifier') {

    $id  = (int)$_POST['id'];
    $qte = (int)$_POST['quantite'];

    // Si la quantité est 0 ou négative → supprime le produit
    if ($qte <= 0) {
        unset($_SESSION['panier'][$id]);
    } else {
        // Sinon → met à jour la quantité
        $_SESSION['panier'][$id]['quantite'] = $qte;
    }

    // Redirige vers le panier
    header('Location: panier.php');
    exit;
}

// ============================================
// CAS 3 : SUPPRIMER UN PRODUIT
// ============================================
if ($action === 'supprimer') {

    $id = (int)$_POST['id'];

    // Supprime le produit du tableau de session
    unset($_SESSION['panier'][$id]);

    // Redirige vers le panier
    header('Location: panier.php');
    exit;
}

// Si l'action n'est pas reconnue → retour accueil
header('Location: index.php');
exit;
?>