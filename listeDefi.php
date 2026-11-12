<?php
session_start();
require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $sql = 'SELECT d.id_defi, d.titre, d.description, d.date_creation, d.etat_defi, b.nom AS nom_brute, b.pseudo_joueur FROM Defi d INNER JOIN Brute b ON b.id_brute = d.id_brute_auteur ORDER BY d.date_creation DESC';
    $defis = $pdo->query($sql)->fetchAll();
} catch (Throwable $e) {
    $defis = [];
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

    <?php if ($defis === []): ?>
        <div class="alert alert-info" role="alert">Aucun défi n’a été publié pour le moment.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($defis as $defi): ?>
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>
