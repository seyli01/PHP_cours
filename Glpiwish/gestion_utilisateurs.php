<?php
require 'db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
	$delete_id = (int)$_POST['delete_id'];
	$stmt = $mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
	if ($stmt) {
		$stmt->bind_param("i", $delete_id);
		if ($stmt->execute()) {
			header('Location: gestion_utilisateurs.php?deleted=1');
			exit();
		} else {
			$message = 'Erreur lors de la suppression.';
		}
	} else {
		$message = 'Erreur lors de la préparation de la requête.';
	}
}
$users = [];
$stmt = $mysqli->prepare("SELECT id, email, role FROM utilisateurs ORDER BY id ASC");
if ($stmt) {
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
		$users = $result->fetch_all(MYSQLI_ASSOC);
	}
}
?>

<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gestion des utilisateurs</title>
</head>
<body>
	<h1>Liste des utilisateurs</h1>

	<?php if (isset($_GET['deleted'])): ?>
		<div class="message success">Utilisateur supprimé avec succès.</div>
	<?php endif; ?>

	<?php if (!empty($message)): ?>
		<div class="message error"><?php echo htmlspecialchars($message); ?></div>
	<?php endif; ?>

	<?php if (empty($users)): ?>
		<p>Aucun utilisateur trouvé.</p>
	<?php else: ?>
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Email</th>
					<th>Role</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $user): ?>
					<tr>
						<td><?php echo htmlspecialchars($user['id']); ?></td>
						<td><?php echo htmlspecialchars($user['email']); ?></td>
						<td><?php echo htmlspecialchars($user['role']); ?></td>
						<td>
							<form method="post" style="display:inline-block;" onsubmit="return confirm('Confirmez-vous la suppression de cet utilisateur ?');">
								<input type="hidden" name="delete_id" value="<?php echo (int)$user['id']; ?>">
								<button type="submit" class="btn-delete">Supprimer</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

</body>
</html>


