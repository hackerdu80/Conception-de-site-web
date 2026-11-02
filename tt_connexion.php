<?php 

require_once "param.inc.php";


$pseudo = $_POST["pseudo"] ?? "";
$motDePasse = $_POST["password"] ?? "";

$sql = "SELECT * FROM joueur WHERE pseudo = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pseudo]);

$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);


if ($utilisateur &&
    password_verify($motDePasse, $utilisateur["mot_de_passe"])) {
    echo "Connexion réussie !";
} else {
    echo "Pseudo ou mot de passe incorrect.";
}
?>