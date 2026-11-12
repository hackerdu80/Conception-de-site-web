<?php
session_start();
require __DIR__ . '/param.inc.php';

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $sql = 'SELECT id_brute, pseudo_joueur, nom, description, avatar, points_de_vie, force_brute, defense FROM Brute ORDER BY id_brute DESC';
    $brutes = $pdo->query($sql)->fetchAll();
} catch (Throwable $e) {
    $brutes = [];
    $_SESSION['erreur'] = 'Erreur de chargement des brutes : ' . $e->getMessage();
}

$titre = 'Liste des brutes';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Liste des brutes</h1>
    </div>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <?php if ($brutes === []): ?>
        <div class="alert alert-info" role="alert">Aucune brute n’a été créée pour le moment.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($brutes as $brute): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <?php if (!empty($brute['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($brute['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar de <?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle border" style="width:48px;height:48px;object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                        <?= htmlspecialchars(substr($brute['nom'], 0, 1), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h2 class="h5 mb-1"><?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?></h2>
                                    <small class="text-muted">Propriétaire : <?= htmlspecialchars($brute['pseudo_joueur'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                            </div>

                            <p class="mb-2"><?= nl2br(htmlspecialchars($brute['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>

                            <div class="small text-muted mb-2">
                                <div>PV : <?= (int) $brute['points_de_vie'] ?></div>
                                <div>Force : <?= (int) $brute['force_brute'] ?></div>
                                <div>Défense : <?= (int) $brute['defense'] ?></div>
                            </div>

                            <span class="badge bg-secondary">Avatar</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>
