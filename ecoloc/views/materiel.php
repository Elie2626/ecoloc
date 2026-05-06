<?php
require_once 'controllers/MaterielController.php';
require_once 'dao/UtilisateurDAO.php';

$mc  = new MaterielController();
$id  = (int)($_GET['id'] ?? 0);
$msg = '';

if ($id === 0) {
    header('Location: index.php?page=catalogue');
    exit;
}

$materiel = $mc->getMateriel($id);
if (!$materiel) {
    header('Location: index.php?page=catalogue');
    exit;
}

/* Traitement demande d'emprunt */
if (isset($_GET['action']) && $_GET['action'] === 'emprunter') {
    $auth->requireAbonne();
    $msg = $mc->demanderEmprunt($id, $_SESSION['user_id']);
    if ($msg === 'success') {
        $msg = 'success';
        $materiel = $mc->getMateriel($id);
    }
}

/* Propriétaire */
$userDAO      = new UtilisateurDAO();
$proprietaire = $userDAO->getById($materiel->getIdProprietaire());

/* État → badge class */
$etatClass = match($materiel->getEtat()) {
    'neuf'         => 'neuf',
    'bon_etat'     => 'bon_etat',
    'etat_correct' => 'etat_correct',
    'abime'        => 'abime',
    default        => 'bon_etat'
};
?>

<!-- ── Detail hero header ─────────────────────────── -->
<header class="mat-detail-header">
    <canvas class="shader-bg" data-shader aria-hidden="true"></canvas>
    <div class="container mat-detail-header-inner">

        <!-- Breadcrumb -->
        <nav class="mat-detail-breadcrumb" aria-label="fil d'Ariane">
            <a href="index.php?page=catalogue">Catalogue</a>
            <span>›</span>
            <?= htmlspecialchars($materiel->getNomMateriel()) ?>
        </nav>

        <h1 class="mat-detail-title"><?= htmlspecialchars($materiel->getNomMateriel()) ?></h1>

        <div class="mat-detail-meta">
            <span class="mat-detail-badge">
                <i class="bi bi-clock"></i>
                <?= $materiel->getDureePretMax() ?> jours max
            </span>
            <span class="mat-detail-badge <?= $materiel->isDisponible() ? '' : 'opacity-50' ?>">
                <i class="bi bi-<?= $materiel->isDisponible() ? 'check-circle' : 'x-circle' ?>"></i>
                <?= $materiel->isDisponible() ? 'Disponible' : 'Indisponible' ?>
            </span>
            <span class="mat-detail-badge">
                <i class="bi bi-star"></i>
                <?= htmlspecialchars($materiel->getEtatLabel()) ?>
            </span>
        </div>

    </div>
</header>

<!-- ── Body ──────────────────────────────────────── -->
<div class="container mat-detail-page">

    <?php if ($msg === 'success'): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            Demande d'emprunt envoyée ! En attente de validation par l'administrateur.
        </div>
    <?php elseif (!empty($msg)): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="mat-detail-grid">

        <!-- ── Left: info card ─────────────────────── -->
        <div class="mat-info-card">
            <h3><i class="bi bi-info-circle me-2"></i>Informations</h3>

            <div class="mat-props-list">
                <div class="mat-prop">
                    <div class="mat-prop-icon"><i class="bi bi-tools"></i></div>
                    <div>
                        <div class="mat-prop-label">Caractéristiques</div>
                        <div class="mat-prop-value"><?= htmlspecialchars($materiel->getCaracteristique() ?: '—') ?></div>
                    </div>
                </div>
                <div class="mat-prop">
                    <div class="mat-prop-icon"><i class="bi bi-star"></i></div>
                    <div>
                        <div class="mat-prop-label">État du matériel</div>
                        <div class="mat-prop-value">
                            <span class="mat-etat-badge <?= $etatClass ?>">
                                <?= htmlspecialchars($materiel->getEtatLabel()) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mat-prop">
                    <div class="mat-prop-icon"><i class="bi bi-calendar-range"></i></div>
                    <div>
                        <div class="mat-prop-label">Durée de prêt maximum</div>
                        <div class="mat-prop-value"><?= $materiel->getDureePretMax() ?> jours</div>
                    </div>
                </div>
                <div class="mat-prop">
                    <div class="mat-prop-icon"><i class="bi bi-circle-fill" style="color:<?= $materiel->isDisponible() ? '#16a34a' : '#dc2626' ?>;font-size:.55rem"></i></div>
                    <div>
                        <div class="mat-prop-label">Disponibilité actuelle</div>
                        <div class="mat-prop-value" style="color:<?= $materiel->isDisponible() ? 'var(--green-700)' : '#dc2626' ?>">
                            <?= $materiel->isDisponible() ? 'Disponible maintenant' : 'Actuellement indisponible' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How it works mini -->
            <div style="background:var(--green-50);border-radius:var(--radius-md);padding:1.25rem;margin-top:.5rem">
                <h4 style="font-size:.9rem;font-weight:700;color:var(--green-900);margin-bottom:.75rem">
                    <i class="bi bi-question-circle me-2"></i>Comment ça marche ?
                </h4>
                <ol style="padding-left:1.25rem;margin:0;font-size:.85rem;color:var(--slate-600);line-height:1.8">
                    <li>Connectez-vous à votre compte ECO'LOC</li>
                    <li>Cliquez sur "Demander l'emprunt"</li>
                    <li>Attendez la confirmation de l'administrateur</li>
                    <li>Récupérez le matériel auprès du propriétaire</li>
                </ol>
            </div>
        </div>

        <!-- ── Right: CTA card ─────────────────────── -->
        <div>
            <div class="mat-borrow-card">
                <div class="borrow-price">Gratuit</div>
                <div class="borrow-sub">Prêt entre voisins — aucun frais</div>

                <?php if ($auth->isAbonne() && $materiel->isDisponible()): ?>
                    <a href="index.php?page=materiel&id=<?= $id ?>&action=emprunter"
                       class="btn-borrow"
                       onclick="return confirm('Confirmer la demande d\'emprunt ?')">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Demander l'emprunt
                    </a>
                <?php elseif (!$auth->isConnected()): ?>
                    <a href="index.php?page=login" class="btn-borrow">
                        <i class="bi bi-person"></i>
                        Se connecter pour emprunter
                    </a>
                <?php elseif (!$materiel->isDisponible()): ?>
                    <button class="btn-borrow" disabled style="opacity:.5;cursor:not-allowed;transform:none;box-shadow:none">
                        <i class="bi bi-clock"></i> Indisponible
                    </button>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-borrow">
                        <i class="bi bi-person"></i>
                        Se connecter pour emprunter
                    </a>
                <?php endif; ?>

                <a href="index.php?page=catalogue"
                   style="display:flex;align-items:center;justify-content:center;gap:.4rem;font-size:.85rem;color:var(--slate-500);margin-top:.25rem;padding:.5rem">
                    <i class="bi bi-arrow-left"></i> Retour au catalogue
                </a>

                <!-- Owner mini card -->
                <?php if ($proprietaire): ?>
                    <div class="mat-owner-mini">
                        <div class="mat-owner-avatar">
                            <?= mb_strtoupper(mb_substr($proprietaire->getPrenom(), 0, 1).mb_substr($proprietaire->getNom(), 0, 1)) ?>
                        </div>
                        <div class="mat-owner-info">
                            <div class="owner-name">
                                <?= htmlspecialchars($proprietaire->getPrenom().' '.$proprietaire->getNom()) ?>
                            </div>
                            <div class="owner-role">
                                <i class="bi bi-geo-alt"></i>
                                <?= htmlspecialchars($proprietaire->getAdresse() ?: 'Lyon, France') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Trust badges -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:1rem">
                    <?php foreach ([
                        ['bi-shield-check','Matériel vérifié'],
                        ['bi-people','Communauté locale'],
                        ['bi-leaf','Éco-responsable'],
                        ['bi-clock-history','Réponse <24h'],
                    ] as [$ico, $lbl]): ?>
                        <div style="display:flex;align-items:center;gap:.45rem;font-size:.78rem;color:var(--slate-500)">
                            <i class="bi <?= $ico ?>" style="color:var(--green-600);font-size:.9rem"></i>
                            <?= $lbl ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
