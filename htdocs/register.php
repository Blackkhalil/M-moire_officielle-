<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $nom = trim($_POST['nom'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe2 = $_POST['mot_de_passe2'] ?? '';
    $role = $_POST['role'] ?? 'etudiant';

    if ($nom === '') {
        $error = 'Le nom est obligatoire.';
    } elseif ($email === '') {
        $error = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'L\'email n\'est pas valide.';
    } elseif ($mot_de_passe === '') {
        $error = 'Le mot de passe est obligatoire.';
    } elseif (strlen($mot_de_passe) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($mot_de_passe !== $mot_de_passe2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!in_array($role, ['etudiant', 'visiteur'], true)) {
        $error = 'Rôle invalide.';
    } elseif (register($nom, $email, $mot_de_passe, $role)) {
        redirect_to('login.php?inscription=success');
    } else {
        $error = 'Erreur lors de l\'inscription (email déjà utilisé ?).';
    }
}

$page_title = 'Inscription - SEIC LBS';
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/navbar.php';
?>
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-logo">
                            <img src="images/logo-lbs.png" alt="Logo LBS" class="login-logo-img img-fluid">
                        </div>
                        <h2 class="login-title">Inscription</h2>
                        <p class="login-subtitle">Créez votre compte SEIC</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger login-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="login-form" autocomplete="on">
                        <?= csrf_input() ?>
                        <div class="form-group">
                            <label for="nom" class="form-label"><i class="fa-solid fa-user"></i> Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom complet" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label"><i class="fa-solid fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" required>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Minimum 8 caractères" required minlength="8">
                            <small class="text-muted">Minimum 8 caractères requis</small>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe2" class="form-label"><i class="fa-solid fa-lock"></i> Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe2" name="mot_de_passe2" placeholder="Répétez le mot de passe" required>
                        </div>
                        <div class="form-group">
                            <label for="role" class="form-label"><i class="fa-solid fa-user-tag"></i> Type de compte</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="etudiant" selected>Étudiant</option>
                                <option value="visiteur">Visiteur</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                <strong>Étudiant :</strong> soumettre projets, rapports et présentations<br>
                                <strong>Visiteur :</strong> consulter les projets et laisser des commentaires
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>S'inscrire</span>
                        </button>
                    </form>

                    <div class="login-footer">
                        <p class="login-register-text">Déjà un compte ? <a href="login.php" class="login-register-link">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
