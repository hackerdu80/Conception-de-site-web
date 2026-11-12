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

if ($nom === '') {
    $_SESSION['erreur'] = 'Le nom de la brute est obligatoire.';
    header('Location: creerBrute.php');
    exit;
}

$avatarSelectionne = trim((string) ($_POST['avatar'] ?? ''));
$avatarSelectionne = basename($avatarSelectionne);
$avatarDir = __DIR__ . '/uploads/avatars';
$allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];

if ($avatarSelectionne === '' || !preg_match('/\.(png|jpe?g|gif|webp|svg)$/i', $avatarSelectionne)) {
    $_SESSION['erreur'] = 'Veuillez choisir un avatar parmi ceux disponibles.';
    header('Location: creerBrute.php');
    exit;
}

$avatarPath = 'uploads/avatars/' . $avatarSelectionne;
$cheminPhysique = $avatarDir . DIRECTORY_SEPARATOR . $avatarSelectionne;

if (!is_file($cheminPhysique)) {
    $_SESSION['erreur'] = 'L’avatar sélectionné est introuvable.';
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
        'avatar' => $avatarPath,
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


