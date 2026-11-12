<?php

require_once __DIR__ . '/param.inc.php';

$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pseudo = trim((string) ($_POST['pseudo'] ?? ''));
$motDePasse = (string) ($_POST['password'] ?? '');

$tableCandidates = ['joueur', 'Joueur'];
$table = null;
foreach ($tableCandidates as $candidate) {
    try {
        $pdo->query('SELECT 1 FROM `' . $candidate . '` LIMIT 1');
        $table = $candidate;
        break;
    } catch (PDOException $e) {
        // on essaie la variante suivante si la table n'existe pas avec cette casse
    }
}

if ($table === null) {
    die('Aucune table joueur trouvée. Vérifie le nom exact de la table dans phpMyAdmin.');
}

$sql = 'SELECT * FROM `' . $table . '` WHERE pseudo = :pseudo LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->execute(['pseudo' => $pseudo]);

$utilisateur = $stmt->fetch();

if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe_hash'])) {
    echo 'Connexion réussie !';
} else {
    echo 'Pseudo ou mot de passe incorrect.';
}
