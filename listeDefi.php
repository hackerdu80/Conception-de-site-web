<?php
session_start();
require __DIR__ . '/param.inc.php';

$pseudoJoueur = trim((string) ($_SESSION['pseudo'] ?? ''));
$estConnecte = !empty($_SESSION['pseudo']) || !empty($_SESSION['id_joueur']);

if ($pseudoJoueur === '' && !empty($_SESSION['id_joueur'])) {
    try {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdo->prepare('SELECT pseudo FROM Joueur WHERE id_joueur = :id_joueur LIMIT 1');
        $stmt->execute(['id_joueur' => (int) $_SESSION['id_joueur']]);
        $joueur = $stmt->fetch();
        if ($joueur) {
            $pseudoJoueur = (string) $joueur['pseudo'];
            $_SESSION['pseudo'] = $pseudoJoueur;
        }
    } catch (Throwable $e) {
        $pseudoJoueur = '';
    }
}

$mesBrutes = [];

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $sql = 'SELECT d.id_defi, d.titre, d.description, d.date_creation, d.etat_defi, b.id_brute, b.nom AS nom_brute, b.pseudo_joueur FROM Defi d INNER JOIN Brute b ON b.id_brute = d.id_brute_auteur WHERE d.etat_defi != 2 ORDER BY d.date_creation DESC';
    $defis = $pdo->query($sql)->fetchAll();

    foreach ($defis as $index => $defi) {
        $stmtCandidatures = $pdo->prepare('SELECT c.id_candidature, c.id_brute_adv, c.message, c.date_candidature, c.etat_candidature, b.nom AS nom_adv, b.pseudo_joueur AS pseudo_adv FROM Candidature c INNER JOIN Brute b ON b.id_brute = c.id_brute_adv WHERE c.id_defi = :id_defi ORDER BY c.date_candidature DESC');
        $stmtCandidatures->execute(['id_defi' => (int) $defi['id_defi']]);
        $defis[$index]['candidatures'] = $stmtCandidatures->fetchAll();
    }

    $historiqueDefis = $pdo->query('SELECT d.id_defi, d.titre, d.description, d.date_creation, d.etat_defi, b.nom AS nom_brute, b.pseudo_joueur FROM Defi d INNER JOIN Brute b ON b.id_brute = d.id_brute_auteur WHERE d.etat_defi = 2 ORDER BY d.date_creation DESC')->fetchAll();

    if ($estConnecte && $pseudoJoueur !== '') {
        $stmtBrutes = $pdo->prepare('SELECT id_brute, nom FROM Brute WHERE pseudo_joueur = :pseudo_joueur ORDER BY nom ASC');
        $stmtBrutes->execute(['pseudo_joueur' => $pseudoJoueur]);
        $mesBrutes = $stmtBrutes->fetchAll();
    }
} catch (Throwable $e) {
    $defis = [];
    $historiqueDefis = [];
    $_SESSION['erreur'] = 'Erreur de chargement des défis : ' . $e->getMessage();
}

$titre = 'Liste des défis';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Liste des défis</h1>
    </div>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if ($defis === []): ?>
        <div class="alert alert-info" role="alert">Aucun défi actif n’a été publié pour le moment.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($defis as $defi): ?>
                <?php $estMonDefi = $estConnecte && $pseudoJoueur !== '' && $defi['pseudo_joueur'] === $pseudoJoueur; ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h2 class="h5 mb-1"><?= htmlspecialchars($defi['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
                                    <small class="text-muted">Publiée par <?= htmlspecialchars($defi['pseudo_joueur'], ENT_QUOTES, 'UTF-8') ?> avec <?= htmlspecialchars($defi['nom_brute'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                                <span class="badge bg-primary">État : <?= (int) $defi['etat_defi'] ?></span>
                            </div>
                            <p class="mt-3 mb-2"><?= nl2br(htmlspecialchars($defi['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                            <small class="text-muted">Créé le <?= htmlspecialchars($defi['date_creation'], ENT_QUOTES, 'UTF-8') ?></small>

                            <div class="mt-3">
                                <h3 class="h6">Candidatures</h3>
                                <?php if (empty($defi['candidatures'])): ?>
                                    <p class="text-muted small mb-0">Aucune candidature pour le moment.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($defi['candidatures'] as $candidature): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                                    <div>
                                                        <strong><?= htmlspecialchars($candidature['nom_adv'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                        <span class="text-muted small">par <?= htmlspecialchars($candidature['pseudo_adv'], ENT_QUOTES, 'UTF-8') ?></span>
                                                    </div>
                                                    <?php if ($estConnecte && $estMonDefi): ?>
                                                        <form method="post" action="tt_accepter_candidature.php">
                                                            <input type="hidden" name="id_candidature" value="<?= (int) $candidature['id_candidature'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-warning">Accepter ce défi</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($candidature['message'])): ?>
                                                    <div class="small text-muted mt-1"><?= htmlspecialchars($candidature['message'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($estConnecte && !$estMonDefi): ?>
                                <?php if ($mesBrutes === []): ?>
                                    <div class="mt-3">
                                        <a href="creerBrute.php" class="btn btn-outline-primary btn-sm">Créer une brute pour relever ce défi</a>
                                    </div>
                                <?php else: ?>
                                    <form method="post" action="tt_candidature.php" class="mt-3">
                                        <input type="hidden" name="id_defi" value="<?= (int) $defi['id_defi'] ?>">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-8">
                                                <select name="id_brute_adv" class="form-select" required>
                                                    <option value="">Choisir votre brute</option>
                                                    <?php foreach ($mesBrutes as $brute): ?>
                                                        <option value="<?= (int) $brute['id_brute'] ?>"><?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-success w-100">Combattre</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php elseif ($estConnecte && $estMonDefi): ?>
                                <p class="text-muted small mt-3 mb-0">C’est votre propre défi : vous ne pouvez pas le relever.</p>
                            <?php else: ?>
                                <p class="text-muted small mt-3 mb-0"><a href="connexion.php">Connectez-vous</a> pour relever ce défi.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-5">
        <h2 class="h4 mb-3">Historique des défis terminés</h2>
        <?php if (empty($historiqueDefis)): ?>
            <div class="alert alert-secondary" role="alert">Aucun défi terminé pour le moment.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($historiqueDefis as $defi): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div>
                                <strong><?= htmlspecialchars($defi['titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <div class="small text-muted">Par <?= htmlspecialchars($defi['pseudo_joueur'], ENT_QUOTES, 'UTF-8') ?> — Brute : <?= htmlspecialchars($defi['nom_brute'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <a href="compteRenduCombat.php?id_defi=<?= (int) $defi['id_defi'] ?>" class="btn btn-sm btn-outline-secondary">Voir le combat</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>
