<?php
require_once __DIR__ . '/bootstrap.php';

if (!is_logged_in()) {
    redirect_to_login();
}

$user = current_user();
$role = $user['role'];

if (!in_array($role, ['organisateur'], true)) {
    die('Accès non autorisé');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    require_valid_csrf();
    $uploadDir = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . 'rapports' . DIRECTORY_SEPARATOR;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['pdf_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    if ($fileError === 0) {
        $allowedTypes = ['application/pdf'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedTypes, true) || $fileExt === 'pdf') {
            if ($fileSize <= 10 * 1024 * 1024) {
                $newFileName = uniqid('', true) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                $filePath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $filePath)) {
                    $projetId = $_POST['projet_id'] ?? null;
                    $commentaire = $_POST['commentaire'] ?? '';

                    if ($projetId) {
                        $stmt = $pdo->prepare('
                            INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation, commentaire)
                            VALUES (?, NOW(), ?, \'en_attente\', ?)
                        ');
                        $stmt->execute([$projetId, $newFileName, $commentaire]);
                        $message = 'Fichier PDF importé avec succès !';
                    } else {
                        $error = 'Veuillez sélectionner un projet.';
                    }
                } else {
                    $error = 'Erreur lors de l\'upload du fichier.';
                }
            } else {
                $error = 'Le fichier est trop volumineux (max 10 Mo).';
            }
        } else {
            $error = 'Seuls les fichiers PDF sont autorisés.';
        }
    } else {
        $error = 'Erreur lors de l\'upload du fichier.';
    }
}

$stmt = $pdo->prepare('
    SELECT p.id, p.nom, e.annee
    FROM projets p
    JOIN editions e ON p.id_edition = e.id
    ORDER BY e.annee DESC, p.nom
');
$stmt->execute();
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Import PDF - SEIC LBS';
require __DIR__ . '/partials/head.php';
?>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img img-fluid">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">Lomé Business School</span>
                </div>
            </div>
        </a>
        <div class="navbar-nav ms-auto">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au tableau de bord</span>
            </a>
        </div>
    </div>
</nav>

<section class="import-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="import-card">
                    <div class="import-header">
                        <div class="import-icon"><i class="fas fa-file-upload"></i></div>
                        <h2 class="import-title">Import de fichiers PDF</h2>
                        <p class="import-subtitle">Téléchargez des rapports PDF pour les projets SEIC</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success import-alert">
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger import-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data" class="import-form">
                        <?= csrf_input() ?>
                        <div class="form-group">
                            <label for="projet_id" class="form-label">
                                <i class="fas fa-project-diagram"></i>
                                Projet associé
                            </label>
                            <select class="form-control" id="projet_id" name="projet_id" required>
                                <option value="">Sélectionnez un projet</option>
                                <?php foreach ($projets as $projet): ?>
                                    <option value="<?= (int) $projet['id'] ?>">
                                        <?= htmlspecialchars($projet['nom'], ENT_QUOTES, 'UTF-8') ?> (<?= (int) $projet['annee'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pdf_file" class="form-label">
                                <i class="fas fa-file-pdf"></i>
                                Fichier PDF
                            </label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control file-input" id="pdf_file" name="pdf_file" accept=".pdf" required>
                                <div class="file-upload-text">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Glissez-déposez votre fichier PDF ici ou cliquez pour sélectionner</p>
                                    <small>Format accepté : PDF (max 10 Mo)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="commentaire" class="form-label">
                                <i class="fas fa-comment"></i>
                                Commentaire (optionnel)
                            </label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Ajoutez un commentaire sur ce rapport..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-import-submit">
                            <i class="fas fa-upload"></i>
                            <span>Importer le fichier</span>
                        </button>
                    </form>

                    <div class="import-info">
                        <h5>Informations importantes</h5>
                        <ul>
                            <li>Seuls les fichiers PDF sont acceptés</li>
                            <li>Taille maximale : 10 Mo</li>
                            <li>Le fichier sera en attente de validation</li>
                            <li>Un nom unique sera généré automatiquement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const fileInput = document.getElementById('pdf_file');
const fileUploadArea = document.querySelector('.file-upload-area');
const fileUploadText = document.querySelector('.file-upload-text');

fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.classList.add('dragover');
});

fileUploadArea.addEventListener('dragleave', () => {
    fileUploadArea.classList.remove('dragover');
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        updateFileDisplay();
    }
});

fileInput.addEventListener('change', updateFileDisplay);

function updateFileDisplay() {
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        fileUploadText.innerHTML = `
            <i class="fas fa-file-pdf"></i>
            <p><strong>${file.name}</strong></p>
            <small>Taille : ${(file.size / 1024 / 1024).toFixed(2)} Mo</small>
        `;
        fileUploadArea.classList.add('has-file');
    }
}
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
