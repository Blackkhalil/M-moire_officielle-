<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme SEIC - LBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <div class="logo-container">
                    <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img">
                    <div class="logo-text">
                        <span class="logo-title">SEIC</span>
                        <span class="logo-subtitle">Lomé Business School</span>
                    </div>
                </div>
            </a>
            
            <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="toggler-icon"></span>
                <span class="toggler-icon"></span>
                <span class="toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home"></i>
                            <span>Accueil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">
                            <i class="fas fa-star"></i>
                            <span>Fonctionnalités</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="fas fa-info-circle"></i>
                            <span>À propos</span>
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-actions">
                    <a href="login.php" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se connecter</span>
                    </a>
                    <a href="register.php" class="btn btn-outline-primary btn-register">
                        <i class="fas fa-user-plus"></i>
                        <span>S'inscrire</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section class="hero text-center">
        <div class="hero-background"></div>
        <div class="container position-relative">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="title-main">Plateforme SEIC</span>
                    <span class="title-accent">Lomé Business School</span>
                </h1>
                <p class="hero-subtitle">
                    Innovation, Entrepreneuriat et Impact Social<br>
                    Un espace moderne pour gérer, collaborer et réussir la SEIC
                </p>
                <div class="hero-actions">
                    <a href="login.php" class="btn btn-primary btn-hero">
                        <i class="fas fa-rocket"></i>
                        <span>Commencer</span>
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-hero">
                        <i class="fas fa-play"></i>
                        <span>Découvrir</span>
                    </a>
                </div>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Projets</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Éditions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
            </div>
        </div>
    </section>
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Fonctionnalités principales</h2>
                <p class="section-subtitle">Découvrez les outils qui font la force de notre plateforme</p>
            </div>
            
        <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h5 class="feature-title">Centralisation</h5>
                        <p class="feature-description">Toutes les éditions, projets et rapports SEIC accessibles en un seul endroit.</p>
                        <div class="feature-badge">Essentiel</div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h5 class="feature-title">Plan interactif</h5>
                        <p class="feature-description">Localisez facilement groupes et salles sur le campus grâce à une carte interactive.</p>
                        <div class="feature-badge">Innovant</div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <h5 class="feature-title">Dépôt & Validation</h5>
                        <p class="feature-description">Déposez vos rapports et suivez leur validation en temps réel.</p>
                        <div class="feature-badge">Pratique</div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="feature-title">Gestion des groupes</h5>
                        <p class="feature-description">Organisez et gérez facilement les groupes d'étudiants et leurs affectations.</p>
                        <div class="feature-badge">Organisé</div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="feature-title">Tableaux de bord</h5>
                        <p class="feature-description">Visualisez les statistiques et suivez l'avancement des projets en temps réel.</p>
                        <div class="feature-badge">Analytique</div>
            </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="feature-title">Sécurité avancée</h5>
                        <p class="feature-description">Protection des données et accès sécurisé pour tous les utilisateurs.</p>
                        <div class="feature-badge">Sécurisé</div>
            </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="about-title">Pourquoi choisir la plateforme SEIC ?</h2>
                        <p class="about-description">
                            Une expérience fluide, sécurisée et professionnelle pour tous les acteurs de la SEIC : 
                            étudiants, organisateurs, visiteurs. Notre plateforme révolutionne la gestion des événements 
                            académiques avec des outils modernes et intuitifs.
                        </p>
                        <div class="about-features">
                            <div class="about-feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Interface intuitive et moderne</span>
                            </div>
                            <div class="about-feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Accès multi-rôles sécurisé</span>
                            </div>
                            <div class="about-feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Gestion en temps réel</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-visual">
                        <div class="visual-card">
                            <i class="fas fa-graduation-cap"></i>
                            <h4>Excellence académique</h4>
                        </div>
                        <div class="visual-card">
                            <i class="fas fa-lightbulb"></i>
                            <h4>Innovation continue</h4>
                        </div>
                        <div class="visual-card">
                            <i class="fas fa-handshake"></i>
                            <h4>Collaboration efficace</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand">
                        <img src="images/logo-lbs.png" alt="Logo LBS" class="footer-logo">
                        <h5>SEIC - LBS</h5>
                        <p>Plateforme d'excellence pour l'innovation et l'entrepreneuriat</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title">Navigation</h6>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-home"></i> Accueil</a></li>
                        <li><a href="#features"><i class="fas fa-star"></i> Fonctionnalités</a></li>
                        <li><a href="#about"><i class="fas fa-info-circle"></i> À propos</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-title">Accès rapide</h6>
                    <ul class="footer-links">
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                        <li><a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-title">Contact</h6>
                    <div class="footer-contact">
                        <p><i class="fas fa-globe"></i> <a href="https://lome-bs.com/" target="_blank">lome-bs.com</a></p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@lomebs.com">info@lomebs.com</a></p>
                        <p><i class="fas fa-map-marker-alt"></i> Lomé, Togo</p>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="footer-copyright">© 2024 Lomé Business School — Plateforme SEIC</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation au scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-modern');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
        
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.feature-card, .about-feature').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html> 