<?php
session_start();

if (empty($_SESSION['id_joueur']) && empty($_SESSION['pseudo'])) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer une brute.';
    header('Location: connexion.php');
    exit;
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

                    <form method="post" action="tt_brute.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la brute</label>
                            <input type="text" id="nom" name="nom" class="form-control" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" maxlength="255"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" id="avatar" name="avatar" class="form-control" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp" required>
                            <div class="form-text">Formats acceptés : PNG, JPG, JPEG, GIF, WEBP. Taille max : 2 Mo.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Créer ma brute</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>