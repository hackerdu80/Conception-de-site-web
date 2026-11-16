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

// --- Avatar : image envoyée par l'utilisateur OU un des 3 avatars par défaut ---
$avatarsParDefaut = ['avatar1.jpg', 'avatar2.svg', 'avatar3.svg'];
$fichierEnvoye = $_FILES['avatar_perso'] ?? null;

if ($fichierEnvoye !== null && $fichierEnvoye['error'] !== UPLOAD_ERR_NO_FILE) {
    // L'utilisateur a envoyé sa propre image
    if ($fichierEnvoye['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['erreur'] = 'Erreur lors de l’envoi de l’image (2 Mo maximum).';
        header('Location: creerBrute.php');
        exit;
    }

    if ($fichierEnvoye['size'] > 2 * 1024 * 1024) {
        $_SESSION['erreur'] = 'L’image est trop lourde (2 Mo maximum).';
        header('Location: creerBrute.php');
        exit;
    }

    // On vérifie que c'est vraiment une image (et pas juste l'extension)
    $typesAutorises = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
    $infosImage = @getimagesize($fichierEnvoye['tmp_name']);
    if ($infosImage === false || !isset($typesAutorises[$infosImage[2]])) {
        $_SESSION['erreur'] = 'Le fichier doit être une image JPG, PNG, GIF ou WEBP.';
        header('Location: creerBrute.php');
        exit;
    }

    $dossierPerso = __DIR__ . '/uploads/avatars/perso';
    if (!is_dir($dossierPerso) && !@mkdir($dossierPerso, 0777, true)) {
        $_SESSION['erreur'] = 'Impossible de créer le dossier ' . $dossierPerso . ' : le serveur n’a pas le droit d’écrire dans uploads/avatars (droits à mettre en 777).';
        header('Location: creerBrute.php');
        exit;
    }

    if (!is_writable($dossierPerso)) {
        $_SESSION['erreur'] = 'Le dossier ' . $dossierPerso . ' n’est pas accessible en écriture (droits à mettre en 777).';
        header('Location: creerBrute.php');
        exit;
    }

    // Nom de fichier aléatoire pour éviter les collisions et les noms dangereux
    $nomFichier = bin2hex(random_bytes(8)) . '.' . $typesAutorises[$infosImage[2]];
    if (!move_uploaded_file($fichierEnvoye['tmp_name'], $dossierPerso . '/' . $nomFichier)) {
        $_SESSION['erreur'] = 'Impossible d’enregistrer l’image.';
        header('Location: creerBrute.php');
        exit;
    }

    $avatarPath = 'uploads/avatars/perso/' . $nomFichier;
} else {
    // Sinon : un des 3 avatars proposés
    $avatarSelectionne = basename(trim((string) ($_POST['avatar'] ?? '')));
    if (!in_array($avatarSelectionne, $avatarsParDefaut, true)) {
        $_SESSION['erreur'] = 'Veuillez choisir un avatar ou envoyer une image.';
        header('Location: creerBrute.php');
        exit;
    }
    $avatarPath = 'uploads/avatars/' . $avatarSelectionne;
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


