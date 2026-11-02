<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$titre = 'La Brute';
$estConnecte = !empty($_SESSION['pseudo']);
$pseudoJoueur = $estConnecte ? trim((string) $_SESSION['pseudo']) : '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?> | La Brute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
<a class="visually-hidden-focusable p-2" href="#contenu">Aller au contenu</a>
<header>
    <nav class="navbar navbar-expand-md bg-dark" data-bs-theme="dark" aria-label="Navigation principale">
        <div class="container-fluid">
            <a class="navbar-brand" href="accueil.php">La Brute</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Afficher le menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
                <ul class="navbar-nav me-auto">
                    <!-- Aperçus pour cette première version ; pages de listes à créer ensuite. -->
                    <li class="nav-item"><a class="nav-link" href="listeBrutes.php">Liste des brutes</a></li>
                    <li class="nav-item"><a class="nav-link" href="listeDefis.php">Liste des défis</a></li>
                </ul>
                <ul class="navbar-nav align-items-center gap-2">
                    <?php if ($estConnecte): ?>
                        <li class="nav-item">
                            <span class="nav-link text-white-50 mb-0">Bienvenue, <?= htmlspecialchars($pseudoJoueur, ENT_QUOTES, 'UTF-8') ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="deconnexion.php">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main id="contenu" class="container flex-grow-1 py-4">