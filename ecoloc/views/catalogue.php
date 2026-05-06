<?php
require_once 'controllers/MaterielController.php';

$mc = new MaterielController();

$motcle      = htmlspecialchars(trim($_GET['motcle'] ?? ''));
$idCategorie = (int)($_GET['categorie'] ?? 0);

$materiels  = $mc->getCatalogue($motcle, $idCategorie);
$categories = $mc->getCategories();

/* Category icon mapping — noms réels de la BDD */
$catIcons = [
    'Outillage'   => 'bi-tools',
    'Jardinage'   => 'bi-flower1',
    'Informatique'=> 'bi-laptop',
    'Sport'       => 'bi-bicycle',
    'Bricolage'   => 'bi-hammer',
];

/* Lookup id → nom de catégorie pour l'affichage sur les cartes */
$catNames = [];
foreach ($categories as $cat) {
    $catNames[$cat->getId()] = $cat->getNomCategorie();
}
?>

<!-- ── Catalogue hero header ──────────────────────── -->
<header class="catalogue-header">
    <canvas class="shader-bg" data-shader aria-hidden="true"></canvas>
    <div class="container catalogue-header-inner">
        <span class="catalogue-badge">
            <i class="bi bi-grid"></i>
            <?= count($materiels) ?> matériel<?= count($materiels) > 1 ? 's' : '' ?> disponibles
        </span>
        <h1>Catalogue du matériel</h1>
        <p>Trouvez l'outil qu'il vous faut parmi notre sélection de matériels prêtés par la communauté.</p>

        <!-- Search bar -->
        <form method="GET" action="index.php" class="catalogue-search-wrap">
            <input type="hidden" name="page" value="catalogue">
            <?php if ($idCategorie): ?>
                <input type="hidden" name="categorie" value="<?= $idCategorie ?>">
            <?php endif; ?>
            <div class="input-icon-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="motcle"
                       placeholder="Perceuse, tondeuse, nettoyeur…"
                       value="<?= $motcle ?>"
                       aria-label="Rechercher un matériel">
            </div>
        </form>
    </div>
</header>

<!-- ── Filters + Results ─────────────────────────── -->
<div class="container py-4">

    <!-- Category pills -->
    <div class="category-nav mb-4">
        <a href="index.php?page=catalogue<?= $motcle ? '&motcle='.urlencode($motcle) : '' ?>"
           class="category-pill <?= $idCategorie === 0 ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i> Toutes
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php
            $icon = $catIcons[$cat->getNomCategorie()] ?? 'bi-tag';
            $url  = 'index.php?page=catalogue&categorie='.$cat->getId()
                    .($motcle ? '&motcle='.urlencode($motcle) : '');
            ?>
            <a href="<?= $url ?>"
               class="category-pill <?= $idCategorie === $cat->getId() ? 'active' : '' ?>">
                <i class="bi <?= $icon ?>"></i>
                <?= htmlspecialchars($cat->getNomCategorie()) ?>
            </a>
        <?php endforeach; ?>

        <?php if ($motcle || $idCategorie): ?>
            <a href="index.php?page=catalogue" class="category-pill" style="border-style:dashed;color:var(--slate-500)">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        <?php endif; ?>
    </div>

    <!-- Results -->
    <?php if (empty($materiels)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size:3rem;color:var(--slate-300);display:block;margin-bottom:1rem"></i>
            <p class="text-muted">Aucun matériel trouvé pour cette recherche.</p>
            <a href="index.php?page=catalogue" class="category-pill mt-2">
                <i class="bi bi-arrow-left"></i> Voir tout le catalogue
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
            <?php foreach ($materiels as $m): ?>
                <div class="col">
                    <div class="mat-card">
                        <!-- Image / placeholder -->
                        <?php if ($m->getImage()): ?>
                            <div class="mat-card-img" style="background:none;padding:0">
                                <img src="public/img/<?= htmlspecialchars($m->getImage()) ?>"
                                     alt="<?= htmlspecialchars($m->getNomMateriel()) ?>"
                                     style="width:100%;height:100%;object-fit:cover">
                            </div>
                        <?php else: ?>
                            <div class="mat-card-img">
                                <i class="bi bi-tools"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Body -->
                        <div class="mat-card-body">
                            <?php
                            $catNom  = $catNames[$m->getIdCategorie()] ?? 'Matériel';
                            $catIcon = $catIcons[$catNom] ?? 'bi-tag';
                            $desc    = $m->getCaracteristique();
                            ?>
                            <div class="mat-card-category">
                                <i class="bi <?= $catIcon ?>"></i>
                                <?= htmlspecialchars($catNom) ?>
                            </div>
                            <div class="mat-card-name"><?= htmlspecialchars($m->getNomMateriel()) ?></div>
                            <div class="mat-card-desc"><?= htmlspecialchars(mb_substr($desc, 0, 80)).(mb_strlen($desc) > 80 ? '…' : '') ?></div>
                        </div>

                        <!-- Footer -->
                        <div class="mat-card-footer">
                            <span class="mat-etat-badge <?= htmlspecialchars($m->getEtat()) ?>">
                                <?= htmlspecialchars($m->getEtatLabel()) ?>
                            </span>
                            <?php if (!$m->isDisponible()): ?>
                                <span style="font-size:.78rem;color:var(--slate-500);font-weight:500">
                                    <i class="bi bi-clock"></i> Indisponible
                                </span>
                            <?php else: ?>
                                <a href="index.php?page=materiel&id=<?= $m->getId() ?>" class="mat-card-link">
                                    Voir <i class="bi bi-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
