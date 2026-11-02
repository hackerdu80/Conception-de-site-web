<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.php');
    exit;
}

$pseudo = trim((string) (isset($_POST['pseudo']) ? $_POST['pseudo'] : ''));
$motDePasse = (string) (isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '');

if ($pseudo === '' || $motDePasse === '') {
    $_SESSION['erreur'] = 'Pseudo et mot de passe obligatoires.';
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

    $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO Joueur (pseudo, mot_de_passe_hash) VALUES (:pseudo, :motDePasseHash)');
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
