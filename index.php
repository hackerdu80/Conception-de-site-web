<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$titre = 'Accueil';
$estConnecte = !empty($_SESSION['pseudo']);
$pseudoJoueur = $estConnecte ? trim((string) $_SESSION['pseudo']) : '';
$erreurChargement = '';

$configFile = __DIR__ . '/param.inc.php';
if (!file_exists($configFile)) {
    $_SESSION['erreur'] = 'Fichier de configuration de la base introuvable.';
    header('Location: connexion.php');
    exit;
}
require $configFile; // <-- charge $host, $dbname, $user, $password

$derniersDefis = [];
$dernieresBrutes = [];

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 2 derniers défis, avec le pseudo du joueur qui possède la brute auteur
    $sql = 'SELECT d.id_defi, d.titre, b.pseudo_joueur AS auteur
            FROM Defi d
            INNER JOIN Brute b ON b.id_brute = d.id_brute_auteur
            ORDER BY d.date_creation DESC, d.id_defi DESC
            LIMIT 2';
    $derniersDefis = $pdo->query($sql)->fetchAll();

    // 2 dernières brutes
    $sql = 'SELECT id_brute, nom, avatar, pseudo_joueur AS auteur
            FROM Brute
            ORDER BY id_brute DESC
            LIMIT 2';
    $dernieresBrutes = $pdo->query($sql)->fetchAll();

} catch (Throwable $e) {
    error_log('index.php : ' . $e->getMessage());
    $erreurChargement = 'Impossible de charger les derniers défis et brutes.';
}

require __DIR__ . '/header.inc.php';
?>

<section class="presentation text-center p-4 p-md-5 mb-4" aria-labelledby="titre-accueil">
    <h1 id="titre-accueil" class="display-5 fw-bold">Bienvenue dans l'Arène !</h1>
    <p class="lead mb-3">Crée ta brute, propose des défis et affronte d'autres joueurs dans des combats automatiques sans merci.</p>
</section>

<div class="row g-4">
    <section class="col-12 col-lg-8" aria-labelledby="titre-regles">
        <h2 id="titre-regles">Règles du jeu</h2>
        <div class="card">
            <div class="card-body">
                <h3 class="h5 card-title">Le fonctionnement des combats</h3>
                <p>Chaque brute démarre avec <strong>30 points de vie</strong>, une force aléatoire entre <strong>5 et 8</strong> et une défense égale à <strong>10 moins sa force</strong>.</p>
                <p>Un joueur publie un défi et les autres proposent leur brute. L'auteur choisit un adversaire parmi les candidatures : le serveur résout alors automatiquement le combat.</p>
                <ul class="mb-0">
                    <li>Le premier attaquant est tiré au sort, puis les brutes attaquent à tour de rôle.</li>
                    <li>Dégâts = force de l'attaquant + bonus aléatoire (0 à 2) − défense adverse.</li>
                    <li>Chaque attaque inflige au minimum 1 point de dégât.</li>
                    <li>Le combat s'arrête lorsqu'une brute n'a plus de points de vie.</li>
                    <li>Le résultat est enregistré et chaque brute retrouve ses 30 points de vie au début du combat suivant.</li>
                </ul>
                <?php if ($estConnecte): ?>
                    <div class="mt-3">
                        <a class="btn btn-success me-2" href="creerBrute.php">Créer ma Brute</a>
                        <a class="btn btn-warning" href="creerDefi.php">Proposer un Défi</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php if ($erreurChargement !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreurChargement, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <aside class="col-12 col-lg-4" aria-label="Aperçus de l'arène">
    <section id="defis" class="card mb-4" aria-labelledby="titre-defis">
        <div class="card-header bg-danger text-white">
            <h2 id="titre-defis" class="h6 mb-0">Derniers défis ouverts</h2>
        </div>
        <ul class="list-group list-group-flush">
            <?php if (empty($derniersDefis)): ?>
                <li class="list-group-item text-secondary">Aucun défi pour le moment.</li>
            <?php endif; ?>
            <?php foreach ($derniersDefis as $defi): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                    <span><?= htmlspecialchars($defi['titre'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="badge rounded-pill text-bg-secondary">Par : <?= htmlspecialchars($defi['auteur'], ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section id="brutes" class="card" aria-labelledby="titre-brutes">
        <div class="card-header bg-dark text-white">
            <h2 id="titre-brutes" class="h6 mb-0">Dernières brutes créées</h2>
        </div>
        <ul class="list-group list-group-flush">
            <?php if (empty($dernieresBrutes)): ?>
                <li class="list-group-item text-secondary">Aucune brute pour le moment.</li>
            <?php endif; ?>
            <?php foreach ($dernieresBrutes as $brute): ?>
                <li class="list-group-item d-flex align-items-center gap-2 border-0">
                    <?php if (!empty($brute['avatar'])): ?>
                        <img src="<?= htmlspecialchars($brute['avatar'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="Avatar de <?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?>"
                             class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                    <?php else: ?>
                        <span class="avatar-placeholder" aria-hidden="true"></span>
                    <?php endif; ?>
                    <strong><?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</aside>
</div>
<?php require __DIR__ . '/footer.inc.php'; ?>
