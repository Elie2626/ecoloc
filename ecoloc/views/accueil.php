<?php
require_once 'controllers/MaterielController.php';
$mc = new MaterielController();
$derniers = array_slice($mc->getCatalogue(), 0, 3);
?>

<!-- HERO -->
<section class="hero d-flex align-items-center">
    <!-- WebGL shader background (fallback = gradient CSS) -->
    <canvas class="shader-bg" data-shader aria-hidden="true"></canvas>
    <!-- Dark overlay pour lisibilité du texte -->
    <div class="hero-shader-overlay" aria-hidden="true"></div>

    <div class="container text-center text-white hero-content">
        <span class="hero-badge">
            <i class="bi bi-leaf"></i> Plateforme de partage local
        </span>
        <h1 class="display-4 fw-bold mb-3">
            Prêtez et empruntez <br>du matériel facilement
        </h1>
        <p class="lead mb-4 opacity-75">
            ECO'LOC connecte les particuliers pour partager du matériel.<br>
            Économique, écologique et simple d'utilisation.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="index.php?page=catalogue" class="btn btn-success btn-lg px-5">
                <i class="bi bi-search"></i> Voir le catalogue
            </a>
            <?php if (!$auth->isConnected()): ?>
                <a href="index.php?page=register" class="btn btn-outline-light btn-lg px-5">
                    <i class="bi bi-person-plus"></i> Rejoindre ECO'LOC
                </a>
            <?php endif; ?>
        </div>
        <!-- Scroll hint -->
        <div class="scroll-hint" aria-hidden="true">
            <i class="bi bi-chevron-down"></i>
        </div>
    </div>
</section>

<!-- CHIFFRES -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h3>+50</h3>
                    <p>Matériels disponibles</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon teal">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>+30</h3>
                    <p>Membres actifs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon gold">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <h3>+100</h3>
                    <p>Emprunts réalisés</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COMMENT CA MARCHE -->
<section class="how-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Simple &amp; rapide</span>
            <h2 class="section-title">Comment ça marche ?</h2>
            <p class="section-sub">En trois étapes, accédez à tout le matériel dont vous avez besoin.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">1</div>
                    <h5>Créez votre compte</h5>
                    <p>Inscrivez-vous gratuitement et attendez la validation de votre compte par un administrateur.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">2</div>
                    <h5>Trouvez du matériel</h5>
                    <p>Parcourez le catalogue, filtrez par catégorie et trouvez le matériel dont vous avez besoin.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">3</div>
                    <h5>Faites une demande</h5>
                    <p>Envoyez une demande d'emprunt. Le propriétaire la valide et vous pouvez récupérer le matériel.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DERNIERS MATERIELS -->
<?php if (!empty($derniers)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Derniers matériels ajoutés</h2>
            <a href="index.php?page=catalogue" class="btn btn-outline-primary">
                Voir tout <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($derniers as $m): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 card-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold mb-0">
                                    <?= htmlspecialchars($m->getNomMateriel()) ?>
                                </h5>
                                <span class="badge <?= $m->isDisponible() ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $m->isDisponible() ? 'Disponible' : 'Indisponible' ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                <?= htmlspecialchars($m->getCaracteristique()) ?>
                            </p>
                            <div class="d-flex gap-2 small text-muted">
                                <span><i class="bi bi-clock"></i> <?= $m->getDureePretMax() ?> jours max</span>
                                <span><i class="bi bi-star"></i> <?= htmlspecialchars($m->getEtatLabel()) ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-4 px-4">
                            <a href="index.php?page=materiel&id=<?= $m->getId() ?>" 
                               class="btn btn-primary w-100">
                                Voir le détail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<?php if (!$auth->isConnected()): ?>
<section class="py-5 cta-section text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Prêt à rejoindre la communauté ?</h2>
        <p class="lead opacity-75 mb-4">
            Inscrivez-vous gratuitement et commencez à partager dès aujourd'hui.
        </p>
        <a href="index.php?page=register" class="btn btn-light btn-lg px-5 fw-bold">
            <i class="bi bi-person-plus"></i> Créer mon compte
        </a>
    </div>
</section>
<?php endif; ?>