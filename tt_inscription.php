<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.php');
    exit;
}

$pseudo = trim((string) ($_POST['pseudo'] ?? ''));
$motDePasse = (string) ($_POST['mot_de_passe'] ?? '');

if ($pseudo === '' || $motDePasse === '') {
    $_SESSION['erreur'] = 'Pseudo et mot de passe obligatoires.';
    header('Location: inscription.php');
    exit;
}

$configFile = __DIR__ . '/param.inc.php';
if (!file_exists($configFile)) {
    $_SESSION['erreur'] = 'Fichier de configuration de la base introuvable.';
    header('Location: inscription.php');
    exit;
}

require $configFile;

if (!isset($host, $user, $password, $dbname)) {
    $_SESSION['erreur'] = 'Les identifiants MySQL sont incomplets.';
    header('Location: inscription.php');
    exit;
}

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $table = 'Joueur';

    $pseudoExiste = $pdo->prepare('SELECT 1 FROM `' . $table . '` WHERE pseudo = :pseudo LIMIT 1');
    $pseudoExiste->execute(['pseudo' => $pseudo]);

    if ($pseudoExiste->fetch()) {
        $_SESSION['erreur'] = 'Ce pseudo est déjà utilisé.';
        header('Location: inscription.php');
        exit;
    }

    $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO `' . $table . '` (pseudo, mot_de_passe_hash) VALUES (:pseudo, :motDePasseHash)');
    $insert->execute([
        'pseudo' => $pseudo,
        'motDePasseHash' => $hash,
    ]);

    $_SESSION['pseudo'] = $pseudo;
    $_SESSION['id_joueur'] = (int) $pdo->lastInsertId();
    $_SESSION['message'] = 'Inscription réussie !';
    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: inscription.php');
    exit;
}
