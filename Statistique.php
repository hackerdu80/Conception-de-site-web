<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: connexion.php');
	exit;
}

require_once __DIR__ . '/param.inc.php';

// Tables attendues : brutes(id, nom, proprietaire_id) et
// combats(id, brute1_id, brute2_id, gagnant_id, date_combat).
$sql = "SELECT b.id AS brute_id, b.nom AS brute_nom,
			   adv.nom AS adversaire, c.date_combat,
			   CASE WHEN c.gagnant_id = b.id THEN 'Gagné'
					WHEN c.gagnant_id IS NULL THEN 'En attente'
					ELSE 'Perdu' END AS resultat
		FROM combats c
		JOIN brutes b ON b.id = c.brute1_id OR b.id = c.brute2_id
		JOIN brutes adv ON adv.id = CASE WHEN b.id = c.brute1_id THEN c.brute2_id ELSE c.brute1_id END
		WHERE b.proprietaire_id = :proprietaire
		ORDER BY b.nom, c.date_combat DESC";
$statement = $pdo->prepare($sql);
$statement->execute(['proprietaire' => $_SESSION['user_id']]);
$combats = $statement->fetchAll();

function h($value): string
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mes combats</title>
	<style>
		body{font-family:Arial,sans-serif;max-width:900px;margin:2rem auto;padding:0 1rem;color:#222}
		table{width:100%;border-collapse:collapse;margin:0 0 2rem}th,td{text-align:left;padding:.7rem;border-bottom:1px solid #ddd}
		th{background:#f2f2f2}.gagne{color:#187438}.perdu{color:#b42318}.attente{color:#805b00}
	</style>
</head>
<body>
	<h1>Mes combats</h1>
	<?php if (!$combats): ?>
		<p>Vous n'avez pas encore de combat.</p>
	<?php else: ?>
		<?php $bruteAffichee = null; ?>
		<?php foreach ($combats as $combat): ?>
			<?php if ($bruteAffichee !== $combat['brute_id']): ?>
				<?php if ($bruteAffichee !== null): ?></tbody></table><?php endif; ?>
				<?php $bruteAffichee = $combat['brute_id']; ?>
				<h2><?= h($combat['brute_nom']) ?></h2>
				<table><thead><tr><th>Adversaire</th><th>Date</th><th>Résultat</th></tr></thead><tbody>
			<?php endif; ?>
			<?php $classe = $combat['resultat'] === 'Gagné' ? 'gagne' : ($combat['resultat'] === 'Perdu' ? 'perdu' : 'attente'); ?>
			<tr>
				<td><?= h($combat['adversaire']) ?></td>
				<td><?= $combat['date_combat'] ? h(date('d/m/Y H:i', strtotime($combat['date_combat']))) : '—' ?></td>
				<td class="<?= $classe ?>"><?= h($combat['resultat']) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody></table>
	<?php endif; ?>
</body>
</html>
