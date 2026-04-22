<?php
// ============================================
// CONNEXION BASE DE DONNÉES
// Fichier : config/connexion.php
// ============================================

// Paramètres de connexion PostgreSQL
$host     = 'localhost';
$dbname   = 'panier_db';
$user     = 'postgres';
$password = 'Mysql123a';  

try {
    // Crée une connexion PDO (PHP Data Objects)
    // PDO est plus sécurisé que les anciennes fonctions mysql_*
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Configure PDO pour afficher les erreurs SQL comme des exceptions PHP
    // Sans ça, les erreurs SQL passent silencieusement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configure PDO pour retourner les résultats en tableaux associatifs par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En cas d'erreur, arrête le script et affiche le problème
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>