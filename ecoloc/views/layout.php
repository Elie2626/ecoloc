<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO'LOC — Prêt de matériel entre particuliers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/ecoloc/public/css/style.css">
    <link rel="stylesheet" href="/ecoloc/public/css/pages.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="bi bi-tools"></i> ECO'LOC
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=catalogue">Catalogue</a>
                </li>
                <?php if ($auth->isConnected()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=profil">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($_SESSION['user_prenom']) ?>
                        </a>
                    </li>
                    <?php if ($auth->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=admin">
                                <i class="bi bi-gear"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="index.php?action=logout">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=login">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-success btn-sm ms-2" href="index.php?page=register">
                            <i class="bi bi-person-plus"></i> S'inscrire
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENU -->
<main class="main-content">
    <?php
    $allowed = ['accueil', 'catalogue', 'login', 'register', 'profil', 'admin', 'materiel'];
    if (in_array($page, $allowed)) {
        include "views/{$page}.php";
    } else {
        include "views/accueil.php";
    }
    ?>
</main>

<!-- FOOTER -->
<footer class="footer bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-tools"></i> ECO'LOC
                </h5>
                <p class="text-muted small">
                    Plateforme de prêt de matériel entre particuliers.<br>
                    Partagez, empruntez, économisez.
                </p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Navigation</h6>
                <ul class="list-unstyled small">
                    <li><a href="index.php" class="text-muted text-decoration-none">Accueil</a></li>
                    <li><a href="index.php?page=catalogue" class="text-muted text-decoration-none">Catalogue</a></li>
                    <li><a href="index.php?page=register" class="text-muted text-decoration-none">S'inscrire</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Contact</h6>
                <p class="text-muted small">
                    <i class="bi bi-geo-alt"></i> Lyon, France<br>
                    <i class="bi bi-envelope"></i> contact@ecoloc.fr
                </p>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <p class="text-center text-muted small mb-0">
            &copy; 2026 ECO'LOC — ORT Lyon. Tous droits réservés.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/ecoloc/public/js/shader.js"></script>
<script src="/ecoloc/public/js/main.js"></script>
</body>
</html>