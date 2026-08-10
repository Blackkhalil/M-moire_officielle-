<?php
require_once __DIR__ . '/app/editions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des éditions - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-calendar-days me-2"></i>Gestion des éditions SEIC</h2>
            <p class="dashboard-subtitle">Créez, modifiez et supprimez les éditions</p>
        </div>
    <div class="action-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-calendar-days fa-2x text-primary me-2"></i>
            <h3 class="fw-bold mb-0">Gestion des éditions SEIC</h3>
        </div>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <?= csrf_input() ?>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="number" name="annee" class="form-control" id="annee" placeholder="Année" required>
                    <label for="annee">Année</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="description" class="form-control" id="description" placeholder="Description">
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label fw-bold">Début</label>
                <input type="date" name="date_debut" class="form-control" id="date_debut" placeholder="Début">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label fw-bold">Fin</label>
                <input type="date" name="date_fin" class="form-control" id="date_fin" placeholder="Fin">
            </div>
            <div class="col-md-2">
                <button type="submit" name="ajouter" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Ajouter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Description</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($editions as $edition): ?>
                    <tr>
                        <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($edition['annee']) ?></span></td>
                        <td><?= htmlspecialchars($edition['description']) ?></td>
                        <td><?= htmlspecialchars($edition['date_debut']) ?></td>
                        <td><?= htmlspecialchars($edition['date_fin']) ?></td>
                        <td>
                            <a href="projets_edition.php?id=<?= $edition['id'] ?>" class="btn btn-info btn-sm me-1" title="Voir les projets"><i class="fa-solid fa-eye"></i></a>
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm me-1 js-export-zip"
                                    data-edition-id="<?= (int)$edition['id'] ?>"
                                    data-edition-annee="<?= htmlspecialchars($edition['annee']) ?>"
                                    title="Exporter les fichiers">
                                <i class="fa-solid fa-download"></i>
                            </button>
                            <a href="?supprimer=<?= $edition['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette édition ?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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
                <a href="https://www.facebook.com/share/184CCko87w/?mibextid=wwXIfr" class="text-white text-decoration-none ms-3" target="_blank" rel="noopener" aria-label="Facebook LBS"><i class="fab fa-facebook"></i></a>
                <a href="https://www.linkedin.com/school/lom%C3%A9-business-school/posts/?feedView=all" class="text-white text-decoration-none ms-2" target="_blank" rel="noopener" aria-label="LinkedIn LBS"><i class="fab fa-linkedin"></i></a>
                <a href="https://www.instagram.com/lome_business_school_officiel?igsh=ZGZzMmM0aTBjYzd0" class="text-white text-decoration-none ms-2" target="_blank" rel="noopener" aria-label="Instagram LBS"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<div class="toast-container toast-container-top position-fixed top-0 end-0 p-3"></div>
<script>
function showToast(message, variant = 'danger') {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${variant} border-0`;
    toastEl.role = 'alert';
    toastEl.ariaLive = 'assertive';
    toastEl.ariaAtomic = 'true';
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    container.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

async function downloadZipForEdition(editionId) {
    const infoUrl = `export_zip.php?edition_id=${encodeURIComponent(editionId)}&mode=info`;
    const res = await fetch(infoUrl, { credentials: 'same-origin' });
    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.ok) {
        const msg = data.detail ? `${data.error} (${data.detail})` : (data.error || 'Export impossible.');
        throw new Error(msg);
    }

    const sizeLabel = data.size_mb ? ` (~${data.size_mb} Mo)` : '';
    showToast(
        `Export SEIC ${data.annee} : ${data.files} fichier(s)${sizeLabel}. Téléchargement en cours…`,
        'primary'
    );

    // Téléchargement direct (évite fetch+blob qui échoue sur les gros ZIP, ex. édition 2025 + vidéos)
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.title = 'Export ZIP';
    iframe.src = `export_zip.php?edition_id=${encodeURIComponent(editionId)}`;
    document.body.appendChild(iframe);
    setTimeout(() => iframe.remove(), 180000);
}

document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-export-zip');
    if (!btn) return;
    e.preventDefault();
    const editionId = btn.getAttribute('data-edition-id');
    if (!editionId) return;

    const oldHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    try {
        await downloadZipForEdition(editionId);
    } catch (err) {
        showToast(err.message || 'Erreur lors de l’export.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
    }
});
</script>
</body>
</html> 








