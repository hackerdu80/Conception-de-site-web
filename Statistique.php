<?php
session_start();

// La page est réservée aux utilisateurs connectés.
$pseudoJoueur = trim((string) ($_SESSION['pseudo'] ?? ''));
if ($pseudoJoueur === '') {
    header('Location: index.php');
    exit;
}

$brutes = [];
$erreur = '';

try {
    require __DIR__ . '/param.inc.php';
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $requete = $pdo->prepare(
        'SELECT b.id_brute, b.nom, b.avatar, b.points_de_vie AS vie, b.force_brute, b.defense,
            COUNT(DISTINCT p.id_combat) AS combats,
            COUNT(DISTINCT CASE WHEN p.id_brute_vainqueur = b.id_brute THEN p.id_combat END) AS victoires,
            COUNT(DISTINCT CASE WHEN p.id_brute_vainqueur <> b.id_brute THEN p.id_combat END) AS defaites
         FROM Brute b
         LEFT JOIN (
            SELECT c.id_combat, d.id_brute_auteur AS id_brute, c.id_brute_vainqueur
            FROM Combat c
            INNER JOIN Defi d ON d.id_defi = c.id_defi
            UNION ALL
            SELECT c.id_combat, ca.id_brute_adv AS id_brute, c.id_brute_vainqueur
            FROM Combat c
            INNER JOIN Candidature ca ON ca.id_defi = c.id_defi AND ca.etat_candidature = 2
         ) p ON p.id_brute = b.id_brute
         WHERE b.pseudo_joueur = :pseudo
         GROUP BY b.id_brute, b.nom, b.avatar, b.points_de_vie, b.force_brute, b.defense
         ORDER BY b.nom'
    );
    $requete->execute(['pseudo' => $pseudoJoueur]);
    $brutes = $requete->fetchAll();
} catch (Throwable $e) {
    $erreur = 'Impossible de récupérer les statistiques. Vérifiez la connexion et les tables de la base de données.';
}

$totaux = ['combats' => 0, 'victoires' => 0, 'defaites' => 0];
foreach ($brutes as $brute) {
    foreach ($totaux as $stat => $valeur) {
        $totaux[$stat] += (int) ($brute[$stat] ?? 0);
    }
}

$titre = 'Mes statistiques';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Statistiques de mes brutes</h1>
    </div>

    <p class="text-muted mb-4">Caractéristiques et palmarès de vos brutes.</p>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert-warning" role="alert">
            <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php else: ?>
        <div class="row g-3 mb-4" aria-label="Palmarès total">
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Brutes</div>
                        <div class="fs-3 fw-bold"><?= count($brutes) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Combats</div>
                        <div class="fs-3 fw-bold"><?= $totaux['combats'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Victoires</div>
                        <div class="fs-3 fw-bold"><?= $totaux['victoires'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Défaites</div>
                        <div class="fs-3 fw-bold"><?= $totaux['defaites'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$brutes): ?>
            <div class="alert alert-info" role="alert">
                Vous n'avez pas encore de brute enregistrée.
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Brute</th>
                                    <th>Vie</th>
                                    <th>Force</th>
                                    <th>Défense</th>
                                    <th>Combats</th>
                                    <th>Victoires</th>
                                    <th>Défaites</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($brutes as $brute): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($brute['avatar'])): ?>
                                            <img
                                                class="rounded-circle border"
                                                src="<?= htmlspecialchars((string) $brute['avatar'], ENT_QUOTES, 'UTF-8') ?>"
                                                alt="Avatar de <?= htmlspecialchars((string) ($brute['nom'] ?? 'brute'), ENT_QUOTES, 'UTF-8') ?>"
                                                style="width:52px;height:52px;object-fit:cover;"
                                            >
                                        <?php else: ?>
                                            <span aria-label="Aucun avatar">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($brute['nom'] ?? 'Sans nom'), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int) ($brute['vie'] ?? 0) ?></td>
                                    <td><?= (int) ($brute['force_brute'] ?? 0) ?></td>
                                    <td><?= (int) ($brute['defense'] ?? 0) ?></td>
                                    <td><?= (int) ($brute['combats'] ?? 0) ?></td>
                                    <td><?= (int) ($brute['victoires'] ?? 0) ?></td>
                                    <td><?= (int) ($brute['defaites'] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a class="btn btn-link ps-0 mt-3" href="index.php">← Retour à l'accueil</a>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>
