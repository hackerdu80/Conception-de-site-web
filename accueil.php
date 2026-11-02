<?php
$titre = 'Accueil';

// On remplacera les données plus tard par les resultats de requete SQL A FAIRE LUNDI 02/11

$derniersDefis = [
    ['titre' => 'Duel sanglant', 'auteur' => 'Titan'],
    ['titre' => 'Qui osera ?', 'auteur' => 'NinjaX'],
];
$dernieresBrutes = [
    ['nom' => 'Goliath'],
    ['nom' => 'Valkyrie'],
];

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
            </div>
        </div>
    </section>

    <aside class="col-12 col-lg-4" aria-label="Aperçus de l'arène">
        <section id="defis" class="card mb-4" aria-labelledby="titre-defis">
            <div class="card-header bg-danger text-white">
                <h2 id="titre-defis" class="h6 mb-0">Derniers défis ouverts</h2>
            </div>
            <ul class="list-group list-group-flush">
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
                <?php foreach ($dernieresBrutes as $brute): ?>
                    <li class="list-group-item d-flex align-items-center gap-2 border-0">
                        <!-- A mettre l'avatar de la brute dans le rond -->
                        <span class="avatar-placeholder" aria-hidden="true"></span>
                        <strong><?= htmlspecialchars($brute['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <p class="small text-secondary mt-2">Aperçus de démonstration.</p>
    </aside>
</div>
<?php require __DIR__ . '/footer.inc.php'; ?>
