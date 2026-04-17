<?php
$host = 'localhost';
$dbname = 'panier_db';
$user = 'postgres';    
$password = 'Mysql123a';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // afficher les erreurs invisible de pdo
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connexion reussie ";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>