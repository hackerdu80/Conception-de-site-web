<?php
session_start();
$titre = 'Connexion';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Connexion à votre compte</h1>

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

                    <form method="post" action="tt_connexion.php">
                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Pseudo</label>
                            <input type="text" id="pseudo" name="pseudo" class="form-control" placeholder="Votre pseudo" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>