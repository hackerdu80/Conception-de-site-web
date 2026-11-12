// 	1	id_brute Primaire	int(11)			Non	Aucun(e)		AUTO_INCREMENT	Modifier Modifier	Supprimer Supprimer	
//	2	pseudo_joueur Index	varchar(50)	utf8mb4_uca1400_ai_ci		Non	Aucun(e)			Modifier Modifier	Supprimer Supprimer	
//	3	nom	varchar(100)	utf8mb4_uca1400_ai_ci		Non	Aucun(e)			Modifier Modifier	Supprimer Supprimer	
//	4	description	varchar(255)	utf8mb4_uca1400_ai_ci		Oui	NULL			Modifier Modifier	Supprimer Supprimer	
//	5	avatar	varchar(255)	utf8mb4_uca1400_ai_ci		Oui	NULL			Modifier Modifier	Supprimer Supprimer	
//	6	points_de_vie	int(11)			Non	Aucun(e)			Modifier Modifier	Supprimer Supprimer	
//	7	force_brute	int(11)			Non	Aucun(e)			Modifier Modifier	Supprimer Supprimer
//	8	defense

<?php
session_start();

$connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $base);

if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo_joueur = trim($_POST['pseudo_joueur'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $avatar = trim($_POST['avatar'] ?? '');

    $points_de_vie = 100;
    $force_brute = 10;
    $defense = 5;

    if ($pseudo_joueur === '' || $nom === '' || $description === '' || $avatar === '') {
        $message = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $pseudo_joueur = mysqli_real_escape_string($connexion, $pseudo_joueur);
        $nom = mysqli_real_escape_string($connexion, $nom);
        $description = mysqli_real_escape_string($connexion, $description);
        $avatar = mysqli_real_escape_string($connexion, $avatar);

        $requete = "INSERT INTO tt_brute (pseudo_joueur, nom, description, avatar, points_de_vie, force_brute, defense)
                    VALUES ('$pseudo_joueur', '$nom', '$description', '$avatar', '$points_de_vie', '$force_brute', '$defense')";

        if (mysqli_query($connexion, $requete)) {
            $message = "Votre brute a bien été créée.";
        } else {
            $message = "Erreur lors de la création de la brute : " . mysqli_error($connexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une brute</title>
</head>
<body>
    <h1>Créer une brute</h1>

    <?php if ($message !== "") : ?>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" action="tt_brute.php">
        <label for="pseudo_joueur">Pseudo du joueur :</label><br>
        <input type="text" name="pseudo_joueur" id="pseudo_joueur" value="<?php echo htmlspecialchars($_SESSION['pseudo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label for="nom">Nom de la brute :</label><br>
        <input type="text" name="nom" id="nom" required><br><br>

        <label for="description">Description :</label><br>
        <textarea name="description" id="description" rows="4" required></textarea><br><br>

        <label for="avatar">Choisir un avatar :</label><br>
        <select name="avatar" id="avatar" required>
            <option value="">-- Choisir un avatar --</option>
            <option value="avatar1.png">Avatar 1</option>
            <option value="avatar2.png">Avatar 2</option>
            <option value="avatar3.png">Avatar 3</option>
            <option value="avatar4.png">Avatar 4</option>
        </select><br><br>

        <input type="submit" value="Créer la brute">
    </form>
</body>
</html>

<?php
mysqli_close($connexion);
?>


