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
$avatarPath = '';

if ($nom === '') {
    $_SESSION['erreur'] = 'Le nom de la brute est obligatoire.';
    header('Location: creerBrute.php');
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    $codeErreurUpload = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
    $messagesErreurUpload = [
        UPLOAD_ERR_OK => 'Aucune erreur.',
        UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur.',
        UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire.',
        UPLOAD_ERR_PARTIAL => 'Le fichier n’a été que partiellement uploadé.',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier n’a été sélectionné.',
        UPLOAD_ERR_NO_TMP_DIR => 'Le dossier temporaire est absent sur le serveur.',
        UPLOAD_ERR_CANT_WRITE => 'L’écriture du fichier sur le disque a échoué.',
        UPLOAD_ERR_EXTENSION => 'Une extension PHP a bloqué l’envoi du fichier.',
    ];

    $_SESSION['erreur'] = 'L’avatar est obligatoire et doit être un fichier valide. ' . ($messagesErreurUpload[$codeErreurUpload] ?? 'Erreur inconnue.');
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

    $fichier = $_FILES['avatar'];
    $extensionsAutorisees = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionsAutorisees, true)) {
        throw new RuntimeException('Type de fichier non autorisé pour l’avatar.');
    }

    if ($fichier['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('L’avatar ne doit pas dépasser 2 Mo.');
    }

    $documentRoot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? __DIR__), DIRECTORY_SEPARATOR);
    $candidatsDossiers = [
        $documentRoot . '/uploads/avatars',
        $documentRoot . '/images/avatars',
        __DIR__ . '/uploads/avatars',
        __DIR__ . '/images/avatars',
    ];

    $dossierCible = null;
    foreach ($candidatsDossiers as $chemin) {
        if ($chemin === '') {
            continue;
        }

        if (!is_dir($chemin) && !mkdir($chemin, 0777, true) && !is_dir($chemin)) {
            continue;
        }

        if (!is_writable($chemin)) {
            @chmod($chemin, 0777);
        }

        if (is_writable($chemin)) {
            $dossierCible = $chemin;
            break;
        }
    }

    if ($dossierCible === null) {
        throw new RuntimeException('Le dossier des avatars n’est pas autorisé en écriture. Vérifie les permissions FTP du dossier uploads/avatars ou images/avatars.');
    }

    $nomFichier = uniqid('avatar_', true) . '.' . $extension;
    $cheminCible = $dossierCible . '/' . $nomFichier;

    if (!move_uploaded_file($fichier['tmp_name'], $cheminCible)) {
        throw new RuntimeException('Le téléchargement de l’avatar a échoué. Vérifie les permissions du dossier d’upload ou la taille du fichier.');
    }

    $avatarPath = 'uploads/avatars/' . $nomFichier;
    if (str_starts_with($dossierCible, rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), DIRECTORY_SEPARATOR) . '/images')) {
        $avatarPath = 'images/avatars/' . $nomFichier;
    }
    if (str_starts_with($dossierCible, rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), DIRECTORY_SEPARATOR) . '/')) {
        $avatarPath = str_replace(rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), DIRECTORY_SEPARATOR) . '/', '', $dossierCible) . '/' . $nomFichier;
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


