<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listeDefi.php');
    exit;
}

if (empty($_SESSION['pseudo']) && empty($_SESSION['id_joueur'])) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour relever un défi.';
    header('Location: connexion.php');
    exit;
}

$idDefi = (int) ($_POST['id_defi'] ?? 0);
$idBruteAdv = (int) ($_POST['id_brute_adv'] ?? 0);
$message = trim((string) ($_POST['message'] ?? ''));

if ($idDefi <= 0 || $idBruteAdv <= 0) {
    $_SESSION['erreur'] = 'Le défi et votre brute sont obligatoires.';
    header('Location: listeDefi.php');
    exit;
}

require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pseudoJoueur = trim((string) ($_SESSION['pseudo'] ?? ''));
    if ($pseudoJoueur === '' && !empty($_SESSION['id_joueur'])) {
        $stmt = $pdo->prepare('SELECT pseudo FROM Joueur WHERE id_joueur = :id_joueur LIMIT 1');
        $stmt->execute(['id_joueur' => (int) $_SESSION['id_joueur']]);
        $joueur = $stmt->fetch();
        if ($joueur) {
            $pseudoJoueur = (string) $joueur['pseudo'];
            $_SESSION['pseudo'] = $pseudoJoueur;
        }
    }

    if ($pseudoJoueur === '') {
        throw new RuntimeException('Impossible de retrouver votre pseudo.');
    }

    $checkDefi = $pdo->prepare('SELECT d.id_defi, b.pseudo_joueur AS pseudo_auteur FROM Defi d INNER JOIN Brute b ON b.id_brute = d.id_brute_auteur WHERE d.id_defi = :id_defi LIMIT 1');
    $checkDefi->execute(['id_defi' => $idDefi]);
    $defi = $checkDefi->fetch();

    if (!$defi) {
        throw new RuntimeException('Ce défi n’existe pas.');
    }

    if ((string) $defi['pseudo_auteur'] === $pseudoJoueur) {
        throw new RuntimeException('Vous ne pouvez pas relever votre propre défi.');
    }

    $checkBrute = $pdo->prepare('SELECT id_brute FROM Brute WHERE id_brute = :id_brute AND pseudo_joueur = :pseudo_joueur LIMIT 1');
    $checkBrute->execute([
        'id_brute' => $idBruteAdv,
        'pseudo_joueur' => $pseudoJoueur,
    ]);

    if (!$checkBrute->fetch()) {
        throw new RuntimeException('Cette brute ne vous appartient pas.');
    }

    $checkDoublon = $pdo->prepare('SELECT c.id_candidature FROM Candidature c INNER JOIN Brute b ON b.id_brute = c.id_brute_adv WHERE c.id_defi = :id_defi AND b.pseudo_joueur = :pseudo_joueur LIMIT 1');
    $checkDoublon->execute([
        'id_defi' => $idDefi,
        'pseudo_joueur' => $pseudoJoueur,
    ]);

    if ($checkDoublon->fetch()) {
        throw new RuntimeException('Vous avez déjà candidaté à ce défi avec une de vos brutes.');
    }

    $insert = $pdo->prepare('INSERT INTO Candidature (id_defi, id_brute_adv, message, date_candidature, etat_candidature) VALUES (:id_defi, :id_brute_adv, :message, NOW(), 1)');
    $insert->execute([
        'id_defi' => $idDefi,
        'id_brute_adv' => $idBruteAdv,
        'message' => $message !== '' ? $message : null,
    ]);

    $_SESSION['message'] = 'Votre candidature a bien été enregistrée.';
    header('Location: listeDefi.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur : ' . $e->getMessage();
    header('Location: listeDefi.php');
    exit;
}
