<?php
// Démarrer la session pour pouvoir utiliser $_SESSION
session_start();

// Récupérer l'action envoyée par le formulaire (ajouter, modifier, supprimer)
// Si aucune action n'est envoyée, on met une valeur vide
$action = $_POST['action'] ?? '';

// =======================
// CAS 1 : AJOUTER PRODUIT
// =======================
if ($action === 'ajouter') {

    // Récupérer les informations du produit depuis le formulaire
    $id = $_POST['id'];          // identifiant du produit
    $nom = $_POST['nom'];        // nom du produit
    $prix = $_POST['prix'];      // prix du produit
    $qte = (int)$_POST['quantite']; // quantité (convertie en entier)

    // Vérifier si le produit existe déjà dans le panier
    if (isset($_SESSION['panier'][$id])) {

        // Si oui → on ajoute la quantité
        $_SESSION['panier'][$id]['quantite'] += $qte;

    } else {

        // Sinon → on crée un nouveau produit dans le panier
        $_SESSION['panier'][$id] = [
            'nom' => $nom,
            'prix' => $prix,
            'quantite' => $qte
        ];
    }
}

// =======================
// CAS 2 : MODIFIER QUANTITÉ
// =======================
if ($action === 'modifier') {

    // Récupérer l'id du produit et la nouvelle quantité
    $id = $_POST['id'];
    $qte = (int)$_POST['quantite'];

    // Si la quantité est 0 ou négative
    if ($qte <= 0) {

        // On supprime le produit du panier
        unset($_SESSION['panier'][$id]);

    } else {

        // Sinon → on met à jour la quantité
        $_SESSION['panier'][$id]['quantite'] = $qte;
    }
}

// =======================
// CAS 3 : SUPPRIMER PRODUIT
// =======================
if ($action === 'supprimer') {

    // Récupérer l'id du produit
    $id = $_POST['id'];

    // Supprimer le produit du panier
    unset($_SESSION['panier'][$id]);
}

// =======================
// REDIRECTION
// =======================

// Après chaque action, on redirige vers la page panier.php
header('Location: panier.php');

// Terminer le script
exit;
?>