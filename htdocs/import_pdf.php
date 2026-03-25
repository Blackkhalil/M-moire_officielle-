<?php
require_once __DIR__ . '/../app/auth.php';

// VÃ©rifier la connexion
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = current_user();
$role = $user['role'];

// VÃ©rifier les permissions
if (!in_array($role, ['administrateur', 'organisateur'])) {
    die('AccÃ¨s non autorisÃ©');
}

require_once __DIR__ . '/../config/config.php';

$message = '';
$error = '';

// Traitement de l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    require_valid_csrf();
    $uploadDir = __DIR__ . '/../uploads/';
    
    // VÃ©rifier que le dossier uploads existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $file = $_FILES['pdf_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];
    
    // VÃ©rifier les erreurs d'upload
    if ($fileError === 0) {
        // VÃ©rifier le type de fichier
        $allowedTypes = ['application/pdf'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileType, $allowedTypes) || $fileExt === 'pdf') {
            // VÃ©rifier la taille (max 10MB)
            if ($fileSize <= 10 * 1024 * 1024) {
                // GÃ©nÃ©rer un nom unique
                $newFileName = uniqid() . '_' . $fileName;
                $filePath = $uploadDir . $newFileName;
                
                // DÃ©placer le fichier
                if (move_uploaded_file($fileTmpName, $filePath)) {
                    // Enregistrer en base de donnÃ©es
                    $projetId = $_POST['projet_id'] ?? null;
                    $commentaire = $_POST['commentaire'] ?? '';
                    
                    if ($projetId) {
                        $stmt = $pdo->prepare("
                            INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation, commentaire) 
                            VALUES (?, NOW(), ?, 'en_attente', ?)
                        ");
                        $stmt->execute([$projetId, $newFileName, $commentaire]);
                        $message = 'Fichier PDF importÃ© avec succÃ¨s !';
                    } else {
                        $error = 'Veuillez sÃ©lectionner un projet.';
                    }
                } else {
                    $error = 'Erreur lors de l\'upload du fichier.';
                }
            } else {
                $error = 'Le fichier est trop volumineux (max 10MB).';
            }
        } else {
            $error = 'Seuls les fichiers PDF sont autorisÃ©s.';
        }
    } else {
        $error = 'Erreur lors de l\'upload du fichier.';
    }
}

// RÃ©cupÃ©rer la liste des projets
$stmt = $pdo->prepare("
    SELECT p.id, p.nom, e.annee 
    FROM projets p 
    JOIN editions e ON p.id_edition = e.id 
    ORDER BY e.annee DESC, p.nom
");
$stmt->execute();
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Import PDF - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img img-fluid">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">LomÃ© Business School</span>
                </div>
            </div>
        </a>
        <div class="navbar-nav ms-auto">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au Dashboard</span>
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
                        <div class="import-icon">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <h2 class="import-title">Import de fichiers PDF</h2>
                        <p class="import-subtitle">TÃ©lÃ©chargez des rapports PDF pour les projets SEIC</p>
                    </div>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success import-alert">
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars($message) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger import-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data" class="import-form">
                        <?= csrf_input() ?>
                        <div class="form-group">
                            <label for="projet_id" class="form-label">
                                <i class="fas fa-project-diagram"></i>
                                Projet associÃ©
                            </label>
                            <select class="form-control" id="projet_id" name="projet_id" required>
                                <option value="">SÃ©lectionnez un projet</option>
                                <?php foreach ($projets as $projet): ?>
                                    <option value="<?= $projet['id'] ?>">
                                        <?= htmlspecialchars($projet['nom']) ?> (<?= $projet['annee'] ?>)
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
                                    <p>Glissez-dÃ©posez votre fichier PDF ici ou cliquez pour sÃ©lectionner</p>
                                    <small>Format acceptÃ© : PDF (max 10MB)</small>
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
                        <h5>Informations importantes :</h5>
                        <ul>
                            <li>Seuls les fichiers PDF sont acceptÃ©s</li>
                            <li>Taille maximale : 10MB</li>
                            <li>Le fichier sera en attente de validation</li>
                            <li>Un nom unique sera gÃ©nÃ©rÃ© automatiquement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                <strong>Â© 2024 LomÃ© Business School</strong> â€” Plateforme SEIC
            </div>
            <div class="col-md-6 text-md-end text-center">
                <a href="https://lome-bs.com/" class="text-white text-decoration-none me-3" target="_blank"><i class="fas fa-globe me-1"></i>Site LBS</a>
                <a href="mailto:info@lomebs.com" class="text-white text-decoration-none"><i class="fas fa-envelope me-1"></i>Contact</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    // Gestion du drag & drop
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
                <small>Taille : ${(file.size / 1024 / 1024).toFixed(2)} MB</small>
            `;
            fileUploadArea.classList.add('has-file');
        }
    }
</script>
</body>
</html>










