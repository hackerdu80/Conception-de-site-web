<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: creerBrute.php');
    exit;
}

$idJoueur = (int) ($_SESSION['id_joueur'] ?? 0);
$pseudoJoueur = trim((string) ($_SESSION['pseudo'] ?? ''));

if ($idJoueur <= 0 && $pseudoJoueur === '') {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer une brute.';
    header('Location: connexion.php');
    exit;
}

$nom = trim((string) ($_POST['nom'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$avatar = trim((string) ($_POST['avatar'] ?? ''));

if ($nom === '' || $avatar === '') {
    $_SESSION['erreur'] = 'Nom et avatar obligatoires.';
    header('Location: creerBrute.php');
    exit;
}

$configFile = __DIR__ . '/param.inc.php';
if (!file_exists($configFile)) {
    $_SESSION['erreur'] = 'Fichier de configuration de la base introuvable.';
    header('Location: creerBrute.php');
    exit;
}

require_once $configFile;

if (!isset($host, $user, $password, $dbname)) {
    $_SESSION['erreur'] = 'Identifiants MySQL incomplets.';
    header('Location: creerBrute.php');
    exit;
}

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if ($pseudoJoueur === '') {
        $stmt = $pdo->prepare('SELECT pseudo FROM Joueur WHERE id_joueur = :id_joueur LIMIT 1');
        $stmt->execute(['id_joueur' => $idJoueur]);
        $joueur = $stmt->fetch();

        if ($joueur) {
            $pseudoJoueur = (string) $joueur['pseudo'];
            $_SESSION['pseudo'] = $pseudoJoueur;
        }
    }

    if ($pseudoJoueur === '') {
        throw new RuntimeException('Impossible de retrouver le pseudo de l’utilisateur connecté.');
    }

    $forceBrute = random_int(5, 8);
    $defense = 10 - $forceBrute;

    $insert = $pdo->prepare('INSERT INTO Brute (pseudo_joueur, nom, description, avatar, points_de_vie, force_brute, defense) VALUES (:pseudo_joueur, :nom, :description, :avatar, 30, :force_brute, :defense)');
    $insert->execute([
        'pseudo_joueur' => $pseudoJoueur,
        'nom' => $nom,
        'description' => $description !== '' ? $description : null,
        'avatar' => $avatar,
        'force_brute' => $forceBrute,
        'defense' => $defense,
    ]);

    $_SESSION['message'] = 'Brute créée avec succès !';
    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: creerBrute.php');
    exit;
}


