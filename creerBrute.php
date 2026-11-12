<?php
session_start();

if (empty($_SESSION['id_joueur']) && empty($_SESSION['pseudo'])) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer une brute.';
    header('Location: connexion.php');
    exit;
}

$avatarDir = __DIR__ . '/uploads/avatars';
$avatarsDisponibles = [];
if (is_dir($avatarDir)) {
    $fichiers = glob($avatarDir . '/*');
    if (is_array($fichiers)) {
        foreach ($fichiers as $fichier) {
            if (is_file($fichier)) {
                $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
                if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true)) {
                    $avatarsDisponibles[] = 'uploads/avatars/' . basename($fichier);
                }
            }
        }
    }
}

sort($avatarsDisponibles, SORT_STRING);

if ($avatarsDisponibles === []) {
    $avatarsDisponibles = [
        'uploads/avatars/avatar1.svg',
        'uploads/avatars/avatar2.svg',
        'uploads/avatars/avatar3.svg',
    ];
}

$titre = 'Créer ma brute';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Créer ma brute</h1>

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

                    <form method="post" action="tt_brute.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la brute</label>
                            <input type="text" id="nom" name="nom" class="form-control" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" maxlength="255"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar disponible</label>
                            <select id="avatar" name="avatar" class="form-select" required>
                                <option value="">Choisir un avatar</option>
                                <?php foreach ($avatarsDisponibles as $avatar): ?>
                                    <option value="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(basename($avatar), ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Choisissez uniquement parmi les avatars disponibles.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Créer ma brute</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>