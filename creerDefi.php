<?php
session_start();
$titre = 'Création de ma brute';
require __DIR__ . '/header.inc.php';
?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Créer ma brute </h1>

                    <form method="post" action="tt_inscription.php">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Nom</label>
                            <input type="text" id="titre" name="titre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" id="description" name="description" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">S’inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>