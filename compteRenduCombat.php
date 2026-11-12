<?php
session_start();

$idDefi = (int) ($_GET['id_defi'] ?? 0);
if ($idDefi <= 0) {
    $_SESSION['erreur'] = 'Aucun combat trouvé.';
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

    $stmt = $pdo->prepare('SELECT c.id_combat, c.date_combat, c.compte_rendu, c.id_brute_vainqueur, d.titre, d.id_brute_auteur, bAuteur.nom AS nom_auteur, bAuteur.pseudo_joueur AS pseudo_auteur, bVainqueur.nom AS nom_vainqueur FROM Combat c INNER JOIN Defi d ON d.id_defi = c.id_defi INNER JOIN Brute bAuteur ON bAuteur.id_brute = d.id_brute_auteur INNER JOIN Brute bVainqueur ON bVainqueur.id_brute = c.id_brute_vainqueur WHERE c.id_defi = :id_defi LIMIT 1');
    $stmt->execute(['id_defi' => $idDefi]);
    $combat = $stmt->fetch();
} catch (Throwable $e) {
    $combat = null;
    $_SESSION['erreur'] = 'Erreur de chargement du combat : ' . $e->getMessage();
}

$titre = 'Compte-rendu du combat';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Compte-rendu du combat</h1>

                    <?php if (isset($_SESSION['erreur'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php unset($_SESSION['erreur']); ?>
                    <?php endif; ?>

                    <?php if (!$combat): ?>
                        <div class="alert alert-info" role="alert">Aucun combat validé pour ce défi.</div>
                    <?php else: ?>
                        <p class="mb-2"><strong>Défi :</strong> <?= htmlspecialchars($combat['titre'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-2"><strong>Brute auteur :</strong> <?= htmlspecialchars($combat['nom_auteur'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($combat['pseudo_auteur'], ENT_QUOTES, 'UTF-8') ?>)</p>
                        <p class="mb-3"><strong>Vainqueur :</strong> <?= htmlspecialchars($combat['nom_vainqueur'], ENT_QUOTES, 'UTF-8') ?></p>

                        <div class="border rounded p-3 bg-light">
                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($combat['compte_rendu'], ENT_QUOTES, 'UTF-8') ?></pre>
                        </div>

                        <div class="mt-4">
                            <a href="listeDefi.php" class="btn btn-primary">Retour à la liste des défis</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>
