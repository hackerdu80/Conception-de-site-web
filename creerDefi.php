<?php
session_start();

$estConnecte = !empty($_SESSION['pseudo']) || !empty($_SESSION['id_joueur']);
if (!$estConnecte) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer un défi.';
    header('Location: connexion.php');
    exit;
}

$titre = 'Créer un défi';
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
        throw new RuntimeException('Pseudo non trouvé dans la session.');
    }

    $sql = 'SELECT id_brute, nom FROM Brute WHERE pseudo_joueur = :pseudo_joueur ORDER BY nom ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pseudo_joueur' => $pseudoJoueur]);
    $brutes = $stmt->fetchAll();
} catch (Throwable $e) {
    $brutes = [];
    $_SESSION['erreur'] = 'Impossible de charger vos brutes : ' . $e->getMessage();
}

require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Publier un défi</h1>

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

                    <?php if ($brutes === []): ?>
                        <div class="alert alert-warning" role="alert">
                            Vous n'avez pas encore de brute. Créez d'abord une brute avant de publier un défi.
                        </div>
                        <a href="creerBrute.php" class="btn btn-primary">Créer ma brute</a>
                    <?php else: ?>
                        <form method="post" action="tt_defi.php">
                            <div class="mb-3">
                                <label for="id_brute_auteur" class="form-label">Ma brute</label>
                                <select id="id_brute_auteur" name="id_brute_auteur" class="form-select" required>
                                    <option value="">Choisir une brute</option>
                                    <?php foreach ($brutes as $brute): ?>
                                        <option value="<?= (int) $brute['id_brute'] ?>"><?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre du défi</label>
                                <input type="text" id="titre" name="titre" class="form-control" maxlength="150" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" maxlength="255"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Publier le défi</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>