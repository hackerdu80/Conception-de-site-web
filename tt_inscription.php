<?php
session_start();

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

require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

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
        throw new PDOException('Aucune table joueur trouvée. Vérifie le nom exact de la table dans phpMyAdmin.');
    }

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

    $_SESSION['message'] = 'Inscription réussie !';
    header('Location: inscription.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: inscription.php');
    exit;
}
