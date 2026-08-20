<?php
require_once __DIR__ . '/bootstrap.php';

if (!is_logged_in()) {
	redirect_to_login();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
	http_response_code(400);
	echo 'Paramètre invalide';
	exit;
}

$stmt = $pdo->prepare('SELECT r.fichier, p.nom AS projet_nom FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
	http_response_code(404);
	echo 'Rapport introuvable';
	exit;
}
$filename = $row['fichier'];
$filePath = rtrim(UPLOADS_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'rapports' . DIRECTORY_SEPARATOR . $filename;
$realBase = realpath(UPLOADS_DIR);
$realFile = realpath($filePath);
if ($realFile === false || strpos($realFile, $realBase) !== 0 || !is_file($realFile)) {
	http_response_code(404);
	echo 'Fichier introuvable';
	exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<link rel="icon" type="image/png" href="images/logo-lbs.png">
	<title>Visualiser PDF - SEIC LBS</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="css/modern-style.css?v=2.7">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
	<div class="container-fluid">
		<a class="navbar-brand d-flex align-items-center" href="dashboard.php">
			<div class="logo-container">
				<img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img img-fluid">
				<div class="logo-text">
					<span class="logo-title">SEIC</span>
					<span class="logo-subtitle">Lomé Business School</span>
				</div>
			</div>
		</a>
	</div>
</nav>
<section class="dashboard-section">
	<div class="container">
		<div class="dashboard-header">
			<h2 class="dashboard-title"><i class="fas fa-file-pdf me-2"></i>Visualiser le rapport</h2>
			<p class="dashboard-subtitle"><?php echo htmlspecialchars($row['projet_nom']); ?></p>
		</div>
		<div class="action-card">
			<div class="ratio ratio-16x9">
				<iframe src="<?php echo 'download.php?id=' . $id . '&inline=1'; ?>" title="PDF" frameborder="0"></iframe>
			</div>
			<div class="mt-3">
				<a class="btn btn-primary me-2" href="<?php echo 'download.php?id=' . $id; ?>"><i class="fas fa-download me-1"></i>Télécharger</a>
				<a class="btn btn-outline-primary" href="rapports_admin.php"><i class="fas fa-arrow-left me-1"></i>Retour</a>
			</div>
		</div>
	</div>
</section>
</body>
</html>









