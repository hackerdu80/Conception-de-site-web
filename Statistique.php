<?php
session_start();

// La page est réservée aux utilisateurs connectés.
$userId = $_SESSION['id_utilisateur'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
if (!$userId) {
	header('Location: index.php');
	exit;
}

// connexion.php doit initialiser une connexion PDO dans la variable $pdo.
$connexionFile = __DIR__ . '/connexion.php';
if (is_file($connexionFile)) {
	require_once $connexionFile;
}

$brutes = [];
$erreur = '';
if (isset($pdo) && $pdo instanceof PDO) {
	try {
		$requete = $pdo->prepare(
			'SELECT nom, vie, force, defense, combats, victoires, defaites
			 FROM brutes
			 WHERE id_utilisateur = :utilisateur
			 ORDER BY nom'
		);
		$requete->execute(['utilisateur' => $userId]);
		$brutes = $requete->fetchAll(PDO::FETCH_ASSOC);
	} catch (Throwable $e) {
		$erreur = 'Impossible de récupérer les statistiques. Vérifiez la table brutes et ses colonnes.';
	}
} else {
	$erreur = 'Connexion à la base de données indisponible.';
}

$totaux = ['combats' => 0, 'victoires' => 0, 'defaites' => 0];
foreach ($brutes as $brute) {
	foreach ($totaux as $stat => $valeur) {
		$totaux[$stat] += (int) ($brute[$stat] ?? 0);
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Statistiques de mes brutes</title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; padding: 32px 16px; background: #f3f5f8; color: #202735; font: 16px/1.5 Arial, sans-serif; }
		main { max-width: 1000px; margin: auto; }
		h1 { margin: 0 0 8px; }
		.intro { margin: 0 0 24px; color: #657084; }
		.totaux { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; margin-bottom: 24px; }
		.carte, .tableau { background: #fff; border-radius: 10px; padding: 18px; box-shadow: 0 2px 10px #17203312; }
		.carte span { display: block; color: #657084; font-size: 14px; }
		.carte strong { display: block; margin-top: 5px; font-size: 25px; }
		.tableau { overflow-x: auto; }
		table { width: 100%; border-collapse: collapse; min-width: 680px; }
		th, td { padding: 12px 10px; border-bottom: 1px solid #e8ebf0; text-align: left; }
		th { color: #657084; font-size: 13px; }
		.message { padding: 14px; border-radius: 8px; background: #fff2df; color: #79500d; }
		.retour { display: inline-block; margin-top: 20px; color: #2459b5; text-decoration: none; }
	</style>
</head>
<body>
<main>
	<h1>Statistiques de mes brutes</h1>
	<p class="intro">Caractéristiques et palmarès de vos brutes.</p>

	<?php if ($erreur !== ''): ?>
		<p class="message"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
	<?php else: ?>
		<section class="totaux" aria-label="Palmarès total">
			<div class="carte"><span>Brutes</span><strong><?= count($brutes) ?></strong></div>
			<div class="carte"><span>Combats</span><strong><?= $totaux['combats'] ?></strong></div>
			<div class="carte"><span>Victoires</span><strong><?= $totaux['victoires'] ?></strong></div>
			<div class="carte"><span>Défaites</span><strong><?= $totaux['defaites'] ?></strong></div>
		</section>

		<?php if (!$brutes): ?>
			<p class="message">Vous n'avez pas encore de brute enregistrée.</p>
		<?php else: ?>
			<section class="tableau">
				<table>
					<thead>
						<tr><th>Brute</th><th>Vie</th><th>Force</th><th>Défense</th><th>Combats</th><th>Victoires</th><th>Défaites</th></tr>
					</thead>
					<tbody>
					<?php foreach ($brutes as $brute): ?>
						<tr>
							<td><?= htmlspecialchars((string) ($brute['nom'] ?? 'Sans nom'), ENT_QUOTES, 'UTF-8') ?></td>
							<td><?= (int) ($brute['vie'] ?? 0) ?></td>
							<td><?= (int) ($brute['force'] ?? 0) ?></td>
							<td><?= (int) ($brute['defense'] ?? 0) ?></td>
							<td><?= (int) ($brute['combats'] ?? 0) ?></td>
							<td><?= (int) ($brute['victoires'] ?? 0) ?></td>
							<td><?= (int) ($brute['defaites'] ?? 0) ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<a class="retour" href="index.php">← Retour à l'accueil</a>
</main>
</body>
</html>
