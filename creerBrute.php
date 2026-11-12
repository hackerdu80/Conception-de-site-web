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
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <input type="text" id="Description" name="Description" class="form-control" required>
                        </div>

                        // Choix de l'avatar de la brute, l'utilisateur dépose une image 
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">S’inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.inc.php'; ?>