<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreur'] = 'Accès interdit.';
    header('Location: inscription.php');
    exit;
}

$pseudo = trim((string) (isset($_POST['pseudo']) ? $_POST['pseudo'] : ''));
$motDePasse = (string) (isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '');
$confirmation = (string) (isset($_POST['mot_de_passe_confirmation']) ? $_POST['mot_de_passe_confirmation'] : '');

if ($pseudo === '') {
    $_SESSION['erreur'] = 'Le pseudo est obligatoire.';
    header('Location: inscription.php');
    exit;
}

if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
    $_SESSION['erreur'] = 'Le pseudo doit contenir entre 3 et 50 caractères.';
    header('Location: inscription.php');
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $pseudo)) {
    $_SESSION['erreur'] = 'Le pseudo ne peut contenir que des lettres, chiffres ou underscore.';
    header('Location: inscription.php');
    exit;
}

if ($motDePasse === '') {
    $_SESSION['erreur'] = 'Le mot de passe est obligatoire.';
    header('Location: inscription.php');
    exit;
}

if (strlen($motDePasse) < 8) {
    $_SESSION['erreur'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    header('Location: inscription.php');
    exit;
}

if ($confirmation === '') {
    $_SESSION['erreur'] = 'La confirmation du mot de passe est obligatoire.';
    header('Location: inscription.php');
    exit;
}

if ($motDePasse !== $confirmation) {
    $_SESSION['erreur'] = 'Les deux mots de passe ne correspondent pas.';
    header('Location: inscription.php');
    exit;
}

require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare('SELECT 1 FROM Joueur WHERE pseudo = :pseudo LIMIT 1');
    $stmt->execute(['pseudo' => $pseudo]);

    if ($stmt->fetch()) {
        $_SESSION['erreur'] = 'Ce pseudo est déjà utilisé.';
        header('Location: inscription.php');
        exit;
    }

    $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO Joueur (pseudo, mot_de_passe_hash, date_inscription) VALUES (:pseudo, :motDePasseHash, NOW())');
    $insert->execute([
        'pseudo' => $pseudo,
        'motDePasseHash' => $hash,
    ]);

    $_SESSION['message'] = 'Inscription réussie !';
    header('Location: inscription.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: inscription.php');
    exit;
}
