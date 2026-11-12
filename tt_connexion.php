<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$configFile = __DIR__ . '/param.inc.php';
if (!file_exists($configFile)) {
    die('Fichier de configuration de la base introuvable.');
}

require_once $configFile;

if (!isset($host, $user, $password, $dbname)) {
    die('Identifiants MySQL incomplets.');
}

$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pseudo = trim((string) ($_POST['pseudo'] ?? ''));
$motDePasse = (string) ($_POST['password'] ?? '');

$table = 'Joueur';

$sql = 'SELECT * FROM `' . $table . '` WHERE pseudo = :pseudo LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->execute(['pseudo' => $pseudo]);

$utilisateur = $stmt->fetch();

if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe_hash'])) {
    echo 'Connexion réussie !';
} else {
    echo 'Pseudo ou mot de passe incorrect.';
}
