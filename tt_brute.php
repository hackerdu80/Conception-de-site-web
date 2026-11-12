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

try {
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $table = 'Brute';
    // Recupération du pseudo de l'utilisateur connecté
    $pseudoJoueur = $_SESSION['pseudo'] ?? '';
    // Tirage au hasard de la force de la brute entre 5 et 8
    $forceBrute = random_int(5, 8);
    // Calcul de la défense de la brute
    $defense = 10 - $forceBrute;

    $sql = "INSERT INTO `$table` (pseudo_joueur, nom, description, avatar, points_de_vie, force_brute, defense) VALUES (:pseudo_joueur, :nom, :description, :avatar, 30, :force_brute, :defense)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'pseudo_joueur' => $pseudoJoueur,
        'nom' => $_POST['nom'] ?? '',
        'description' => $_POST['Description'] ?? '',
        'avatar' => $_POST['avatar'] ?? '',
        'force_brute' => $forceBrute,
        'defense' => $defense,
    ]);

    // Redirection vers la page d'accueil après la création de la brute
    $_SESSION['message'] = 'Brute créée avec succès !';
    header('Location: index.php');
    exit;

}
catch (Throwable $e) {
    $_SESSION['erreur'] = 'Erreur SQL : ' . $e->getMessage();
    header('Location: connexion.php');
    exit;
}
?>


