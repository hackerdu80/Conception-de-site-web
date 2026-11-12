<?php
session_start();

// Vérification que la requête est bien de type POST = formulaire soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit;
}

// Récupération des données du formulaire
$pseudo = trim((string) ($_POST['pseudo'] ?? ''));
$motDePasse = (string) ($_POST['password'] ?? '');

if ($pseudo === '' || $motDePasse === '') {
    $_SESSION['erreur'] = 'Pseudo et mot de passe obligatoires.';
    header('Location: connexion.php');
    exit;
}

// Vérification de l'existence du fichier de configuration
$configFile = __DIR__ . '/param.inc.php';
if (!file_exists($configFile)) {
    $_SESSION['erreur'] = 'Fichier de configuration de la base introuvable.';
    header('Location: connexion.php');
    exit;
}

require_once $configFile;

if (!isset($host, $user, $password, $dbname)) {
    $_SESSION['erreur'] = 'Identifiants MySQL incomplets.';
    header('Location: connexion.php');
    exit;
}

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $table = 'Joueur';
    $sql = 'SELECT * FROM `' . $table . '` WHERE pseudo = :pseudo LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pseudo' => $pseudo]);
    $utilisateur = $stmt->fetch();

    // Vérification du mot de passe
    if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe_hash'])) {
        $_SESSION['pseudo'] = $utilisateur['pseudo'];
        $_SESSION['id_joueur'] = $utilisateur['id_joueur'];
        $_SESSION['message'] = 'Connexion réussie !';
        header('Location: index.php');
        exit;
    }

    $_SESSION['erreur'] = 'Pseudo ou mot de passe incorrect.';
    header('Location: connexion.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: connexion.php');
    exit;
}
