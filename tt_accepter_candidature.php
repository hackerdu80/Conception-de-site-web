<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listeDefi.php');
    exit;
}

if (empty($_SESSION['pseudo']) && empty($_SESSION['id_joueur'])) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour accepter un défi.';
    header('Location: connexion.php');
    exit;
}

$idCandidature = (int) ($_POST['id_candidature'] ?? 0);
if ($idCandidature <= 0) {
    $_SESSION['erreur'] = 'Aucune candidature sélectionnée.';
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

    $checkCandidature = $pdo->prepare('SELECT c.id_candidature, c.id_defi, c.id_brute_adv, d.id_brute_auteur, bAuteur.id_brute AS id_brute_auteur_db, bAuteur.pseudo_joueur AS pseudo_auteur, bAdv.nom AS nom_adv, bAdv.pseudo_joueur AS pseudo_adv FROM Candidature c INNER JOIN Defi d ON d.id_defi = c.id_defi INNER JOIN Brute bAuteur ON bAuteur.id_brute = d.id_brute_auteur INNER JOIN Brute bAdv ON bAdv.id_brute = c.id_brute_adv WHERE c.id_candidature = :id_candidature LIMIT 1');
    $checkCandidature->execute(['id_candidature' => $idCandidature]);
    $candidature = $checkCandidature->fetch();

    if (!$candidature) {
        throw new RuntimeException('Cette candidature n’existe pas.');
    }

    if ((string) $candidature['pseudo_auteur'] !== $pseudoJoueur) {
        throw new RuntimeException('Vous ne pouvez accepter que les défis de votre brute.');
    }

    $checkCombat = $pdo->prepare('SELECT id_combat FROM Combat WHERE id_defi = :id_defi LIMIT 1');
    $checkCombat->execute(['id_defi' => (int) $candidature['id_defi']]);
    if ($checkCombat->fetch()) {
        $_SESSION['message'] = 'Ce défi a déjà été traité par un combat.';
        header('Location: listeDefi.php');
        exit;
    }

    $stmtBrutes = $pdo->prepare('SELECT id_brute, nom, force_brute, defense, points_de_vie FROM Brute WHERE id_brute IN (:id_auteur, :id_adv)');
    $stmtBrutes->bindValue(':id_auteur', (int) $candidature['id_brute_auteur_db'], PDO::PARAM_INT);
    $stmtBrutes->bindValue(':id_adv', (int) $candidature['id_brute_adv'], PDO::PARAM_INT);
    $stmtBrutes->execute();
    $brutes = $stmtBrutes->fetchAll();

    if (count($brutes) !== 2) {
        throw new RuntimeException('Les deux brutes du combat sont introuvables.');
    }

    $bruteAuteur = null;
    $bruteAdv = null;
    foreach ($brutes as $brute) {
        if ((int) $brute['id_brute'] === (int) $candidature['id_brute_auteur_db']) {
            $bruteAuteur = $brute;
        }
        if ((int) $brute['id_brute'] === (int) $candidature['id_brute_adv']) {
            $bruteAdv = $brute;
        }
    }

    if ($bruteAuteur === null || $bruteAdv === null) {
        throw new RuntimeException('Les statistiques des brutes sont manquantes.');
    }

    $pvAuteur = 30;
    $pvAdv = 30;
    $tour = 1;
    $ordre = [
        'auteur' => $bruteAuteur,
        'adversaire' => $bruteAdv,
    ];
    $premier = random_int(0, 1) === 0 ? 'auteur' : 'adversaire';
    $tourActuel = $premier;

    $lignes = [];
    $lignes[] = 'Début du combat entre ' . $bruteAuteur['nom'] . ' et ' . $bruteAdv['nom'] . '.';

    while ($pvAuteur > 0 && $pvAdv > 0) {
        $attaquantKey = $tourActuel;
        $defenseurKey = $attaquantKey === 'auteur' ? 'adversaire' : 'auteur';
        $attaquant = $ordre[$attaquantKey];
        $defenseur = $ordre[$defenseurKey];

        $degats = (int) $attaquant['force_brute'] + random_int(0, 2) - (int) $defenseur['defense'];
        $degats = max(1, $degats);

        if ($defenseurKey === 'auteur') {
            $pvAuteur = max(0, $pvAuteur - $degats);
            $pvDefenseur = $pvAuteur;
        } else {
            $pvAdv = max(0, $pvAdv - $degats);
            $pvDefenseur = $pvAdv;
        }

        $lignes[] = 'Tour ' . $tour . ' : ' . $attaquant['nom'] . ' attaque ' . $defenseur['nom'] . ' pour ' . $degats . ' dégâts. ' . $defenseur['nom'] . ' a maintenant ' . $pvDefenseur . ' PV.';

        $tourActuel = $defenseurKey;
        $tour++;
    }

    if ($pvAuteur <= 0 && $pvAdv <= 0) {
        $vainqueurId = random_int(0, 1) === 0 ? (int) $bruteAuteur['id_brute'] : (int) $bruteAdv['id_brute'];
        $vainqueurNom = $vainqueurId === (int) $bruteAuteur['id_brute'] ? $bruteAuteur['nom'] : $bruteAdv['nom'];
        $lignes[] = 'Les deux brutes sont réduites à 0 PV. Le vainqueur est choisi au hasard.';
    } elseif ($pvAuteur <= 0) {
        $vainqueurId = (int) $bruteAdv['id_brute'];
        $vainqueurNom = $bruteAdv['nom'];
    } else {
        $vainqueurId = (int) $bruteAuteur['id_brute'];
        $vainqueurNom = $bruteAuteur['nom'];
    }

    $compteRendu = implode("\n", $lignes) . "\nLe vainqueur est " . $vainqueurNom . ".";

    $insertCombat = $pdo->prepare('INSERT INTO Combat (id_defi, id_brute_vainqueur, date_combat, compte_rendu) VALUES (:id_defi, :id_brute_vainqueur, NOW(), :compte_rendu)');
    $insertCombat->execute([
        'id_defi' => (int) $candidature['id_defi'],
        'id_brute_vainqueur' => (int) $vainqueurId,
        'compte_rendu' => $compteRendu,
    ]);

    $pdo->prepare('UPDATE Candidature SET etat_candidature = 0 WHERE id_defi = :id_defi AND id_candidature != :id_candidature')->execute([
        'id_defi' => (int) $candidature['id_defi'],
        'id_candidature' => (int) $idCandidature,
    ]);

    $pdo->prepare('UPDATE Candidature SET etat_candidature = 2 WHERE id_candidature = :id_candidature')->execute([
        'id_candidature' => (int) $idCandidature,
    ]);

    $pdo->prepare('UPDATE Defi SET etat_defi = 2 WHERE id_defi = :id_defi')->execute([
        'id_defi' => (int) $candidature['id_defi'],
    ]);

    $_SESSION['message'] = 'Combat généré avec succès.';
    header('Location: compteRenduCombat.php?id_defi=' . (int) $candidature['id_defi']);
    exit;
} catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur lors du combat : ' . $e->getMessage();
    header('Location: listeDefi.php');
    exit;
}
