<?php
require_once __DIR__ . '/../app/soumission_rapport.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumettre un rapport - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">Lomé Business School</span>
                </div>
            </div>
        </a>
    </div>
</nav>
<section class="import-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="import-card">
        <div class="text-center mb-3">
                        <div class="import-icon"><i class="fa-solid fa-file-upload"></i></div>
                        <h2 class="import-title">Soumettre un rapport</h2>
                        <p class="import-subtitle">Déposez votre rapport PDF pour la SEIC</p>
        </div>
        <?php if ($success): ?>
                        <div class="alert alert-success import-alert"><i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
                        <div class="alert alert-danger import-alert"><i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
                    <form method="post" enctype="multipart/form-data" class="import-form" autocomplete="on">
                        <div class="form-group">
                            <label for="id_projet" class="form-label"><i class="fa-solid fa-lightbulb me-1"></i> Projet</label>
                            <select class="form-control" id="id_projet" name="id_projet" required>
                                <option value="">Sélectionner un projet</option>
                                <?php foreach ($projets as $projet): ?>
                                    <option value="<?= $projet['id'] ?>"><?= htmlspecialchars($projet['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fichier" class="form-label"><i class="fa-solid fa-file-pdf me-1"></i> Fichier PDF</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control file-input" id="fichier" name="fichier" accept="application/pdf" required>
                                <div class="file-upload-text">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Glissez-déposez votre fichier PDF ici ou cliquez pour sélectionner</p>
                                    <small>Format accepté : PDF</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-import-submit"><i class="fa-solid fa-paper-plane me-1"></i>Soumettre</button>
                        <a href="dashboard.php" class="btn btn-outline-primary btn-export-submit mt-2"><i class="fa-solid fa-arrow-left me-1"></i>Retour</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                <strong>© 2024 Lomé Business School</strong> — Plateforme SEIC
            </div>
            <div class="col-md-6 text-md-end text-center">
                <a href="https://lome-bs.com/" class="text-white text-decoration-none me-3" target="_blank"><i class="fas fa-globe me-1"></i>Site LBS</a>
                <a href="mailto:info@lomebs.com" class="text-white text-decoration-none"><i class="fas fa-envelope me-1"></i>Contact</a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fileInput = document.getElementById('fichier');
const fileUploadArea = document.querySelector('.file-upload-area');
const fileUploadText = document.querySelector('.file-upload-text');
if (fileUploadArea && fileInput) {
  fileUploadArea.addEventListener('dragover', (e) => { e.preventDefault(); fileUploadArea.classList.add('dragover'); });
  fileUploadArea.addEventListener('dragleave', () => fileUploadArea.classList.remove('dragover'));
  fileUploadArea.addEventListener('drop', (e) => { e.preventDefault(); fileUploadArea.classList.remove('dragover'); const files = e.dataTransfer.files; if (files.length > 0) { fileInput.files = files; updateFileDisplay(); } });
  fileInput.addEventListener('change', updateFileDisplay);
}
function updateFileDisplay() {
  if (fileInput.files.length > 0) {
    const file = fileInput.files[0];
    fileUploadText.innerHTML = '<i class="fas fa-file-pdf"></i><p><strong>' + file.name + '</strong></p><small>Taille : ' + (file.size/1024/1024).toFixed(2) + ' MB</small>';
    fileUploadArea.classList.add('has-file');
  }
}
</script>
</body>
</html> 