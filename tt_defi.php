<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: creerDefi.php');
    exit;
}

$idJoueur = (int) ($_SESSION['id_joueur'] ?? 0);
$pseudoJoueur = trim((string) ($_SESSION['pseudo'] ?? ''));

if ($idJoueur <= 0 && $pseudoJoueur === '') {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer un défi.';
    header('Location: connexion.php');
    exit;
}

$titreDefi = trim((string) ($_POST['titre'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$idBruteAuteur = (int) ($_POST['id_brute_auteur'] ?? 0);

if ($titreDefi === '' || $idBruteAuteur <= 0) {
    $_SESSION['erreur'] = 'Titre et brute obligatoires.';
    header('Location: creerDefi.php');
    exit;
}

require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if ($pseudoJoueur === '' && $idJoueur > 0) {
        $stmt = $pdo->prepare('SELECT pseudo FROM Joueur WHERE id_joueur = :id_joueur LIMIT 1');
        $stmt->execute(['id_joueur' => $idJoueur]);
        $joueur = $stmt->fetch();
        if ($joueur) {
            $pseudoJoueur = (string) $joueur['pseudo'];
            $_SESSION['pseudo'] = $pseudoJoueur;
        }
    }

    $checkBrute = $pdo->prepare('SELECT id_brute FROM Brute WHERE id_brute = :id_brute AND pseudo_joueur = :pseudo_joueur LIMIT 1');
    $checkBrute->execute([
        'id_brute' => $idBruteAuteur,
        'pseudo_joueur' => $pseudoJoueur,
    ]);

    if (!$checkBrute->fetch()) {
        $_SESSION['erreur'] = 'Cette brute ne vous appartient pas.';
        header('Location: creerDefi.php');
        exit;
    }

    $insert = $pdo->prepare('INSERT INTO Defi (id_brute_auteur, titre, description, date_creation, etat_defi) VALUES (:id_brute_auteur, :titre, :description, NOW(), 1)');
    $insert->execute([
        'id_brute_auteur' => $idBruteAuteur,
        'titre' => $titreDefi,
        'description' => $description !== '' ? $description : null,
    ]);

    $_SESSION['message'] = 'Défi publié avec succès !';
    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: creerDefi.php');
    exit;
}
