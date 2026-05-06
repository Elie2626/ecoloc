<?php
$auth->requireLogin();

require_once 'controllers/MaterielController.php';
require_once 'dao/EmpruntDAO.php';
require_once 'dao/MaterielDAO.php';
require_once 'dao/CategorieDAO.php';
require_once 'dao/UtilisateurDAO.php';

$mc           = new MaterielController();
$empruntDAO   = new EmpruntDAO();
$materielDAO  = new MaterielDAO();
$categorieDAO = new CategorieDAO();
$userDAO      = new UtilisateurDAO();

$userId       = $_SESSION['user_id'];
$user         = $userDAO->getById($userId);
$mesEmprunts  = $empruntDAO->getByEmprunteur($userId);
$mesMateriels = $mc->getMaterielParProprietaire($userId);
$categories   = $mc->getCategories();

$message     = '';
$messageType = '';

// Ajouter un matériel
if (isset($_POST['ajouter_materiel'])) {
    $nom   = trim($_POST['nom_materiel'] ?? '');
    $carac = trim($_POST['caracteristique'] ?? '');
    $cat   = (int)($_POST['id_categorie'] ?? 0);

    if (empty($nom) || $cat === 0) {
        $message     = 'Le nom et la catégorie sont obligatoires.';
        $messageType = 'danger';
    } elseif (empty($carac)) {
        $message     = 'La description est obligatoire.';
        $messageType = 'danger';
    } else {
        $ok = $mc->ajouterMateriel($_POST, $userId);
        if ($ok) {
            $message      = 'Votre outil a été mis en ligne avec succès !';
            $messageType  = 'success';
            $mesMateriels = $mc->getMaterielParProprietaire($userId);
        } else {
            $message     = "Erreur lors de l'ajout du matériel.";
            $messageType = 'danger';
        }
    }
}

// Supprimer un de ses propres matériels
if (isset($_GET['supprimer_mat'])) {
    $idMat = (int)$_GET['supprimer_mat'];
    $mat   = $materielDAO->getById($idMat);
    if ($mat && $mat->getIdProprietaire() === $userId) {
        $mc->supprimerMateriel($idMat);
        $message      = 'Matériel supprimé.';
        $messageType  = 'success';
        $mesMateriels = $mc->getMaterielParProprietaire($userId);
    }
}

$onglet       = $_GET['onglet'] ?? 'emprunts';
$filtreStatut = $_GET['statut'] ?? 'tous';

$empruntsAffiches = $mesEmprunts;
if ($filtreStatut !== 'tous') {
    $empruntsAffiches = array_filter($mesEmprunts, fn($e) => $e->getStatut() === $filtreStatut);
}

$initiales = strtoupper(mb_substr($user->getPrenom(), 0, 1) . mb_substr($user->getNom(), 0, 1));

$roleBadge = match($user->getRole()) {
    'admin'   => ['label' => 'Administrateur', 'class' => 'badge-role-admin'],
    'abonne'  => ['label' => 'Abonné', 'class' => 'badge-role-abonne'],
    default   => ['label' => 'Visiteur', 'class' => 'badge-role-visiteur'],
};
?>

<div class="profil-page">

    <!-- ── En-tête profil ─────────────────────────── -->
    <div class="profil-header">
        <!-- Shader WebGL (blend screen sur fond vert) -->
        <canvas class="shader-bg shader-bg--profil" data-shader aria-hidden="true"></canvas>
        <div class="container">
            <div class="profil-header-inner">
                <div class="profil-avatar"><?= htmlspecialchars($initiales) ?></div>
                <div class="profil-info">
                    <h1 class="profil-name">
                        <?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?>
                    </h1>
                    <span class="badge-role <?= $roleBadge['class'] ?>">
                        <?= $roleBadge['label'] ?>
                    </span>
                    <div class="profil-meta">
                        <span><i class="bi bi-envelope"></i> <?= htmlspecialchars($user->getEmail()) ?></span>
                        <?php if ($user->getTelephone()): ?>
                            <span><i class="bi bi-telephone"></i> <?= htmlspecialchars($user->getTelephone()) ?></span>
                        <?php endif; ?>
                        <?php if ($user->getAdresse()): ?>
                            <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($user->getAdresse()) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="profil-stats">
                    <div class="profil-stat">
                        <span class="profil-stat-num"><?= count($mesEmprunts) ?></span>
                        <span class="profil-stat-label">Emprunt<?= count($mesEmprunts) > 1 ? 's' : '' ?></span>
                    </div>
                    <div class="profil-stat">
                        <span class="profil-stat-num"><?= count($mesMateriels) ?></span>
                        <span class="profil-stat-label">Outil<?= count($mesMateriels) > 1 ? 's' : '' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Contenu onglets ────────────────────────── -->
    <div class="container profil-body">

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?> mb-4" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Nav onglets -->
        <div class="profil-tabs">
            <a href="index.php?page=profil&onglet=emprunts"
               class="profil-tab <?= $onglet === 'emprunts' ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i>
                Mes emprunts
                <?php
                $enAttente = count(array_filter($mesEmprunts, fn($e) => $e->getStatut() === 'en_attente'));
                if ($enAttente > 0): ?>
                    <span class="tab-badge"><?= $enAttente ?></span>
                <?php endif; ?>
            </a>
            <a href="index.php?page=profil&onglet=outils"
               class="profil-tab <?= $onglet === 'outils' ? 'active' : '' ?>">
                <i class="bi bi-tools"></i>
                Mes outils
                <span class="tab-count"><?= count($mesMateriels) ?></span>
            </a>
            <a href="index.php?page=profil&onglet=ajouter"
               class="profil-tab <?= $onglet === 'ajouter' ? 'active' : '' ?>">
                <i class="bi bi-plus-circle"></i>
                Proposer un outil
            </a>
        </div>

        <!-- ===== ONGLET : MES EMPRUNTS ===== -->
        <?php if ($onglet === 'emprunts'): ?>

            <!-- Filtres statut -->
            <div class="status-filters mb-4">
                <?php
                $statuts = ['tous', 'en_attente', 'en_cours', 'termine', 'refuse'];
                $labels  = [
                    'tous'       => 'Tous',
                    'en_attente' => 'En attente',
                    'en_cours'   => 'En cours',
                    'termine'    => 'Terminés',
                    'refuse'     => 'Refusés',
                ];
                foreach ($statuts as $s):
                    $nb = ($s === 'tous')
                        ? count($mesEmprunts)
                        : count(array_filter($mesEmprunts, fn($e) => $e->getStatut() === $s));
                ?>
                    <a href="index.php?page=profil&onglet=emprunts&statut=<?= $s ?>"
                       class="status-filter <?= $filtreStatut === $s ? 'active' : '' ?>">
                        <?= $labels[$s] ?>
                        <span><?= $nb ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($empruntsAffiches)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Aucun emprunt <?= $filtreStatut !== 'tous' ? '"' . $labels[$filtreStatut] . '"' : '' ?> pour le moment.</p>
                    <a href="index.php?page=catalogue" class="btn-primary-sm">Parcourir le catalogue</a>
                </div>
            <?php else: ?>
                <div class="emprunts-list">
                    <?php foreach ($empruntsAffiches as $e):
                        $mat = $materielDAO->getById($e->getIdMateriel());
                        $badgeClass = match($e->getStatut()) {
                            'en_attente' => 'status-waiting',
                            'en_cours'   => 'status-active',
                            'termine'    => 'status-done',
                            'refuse'     => 'status-refused',
                            default      => 'status-done'
                        };
                        $badgeLabel = match($e->getStatut()) {
                            'en_attente' => 'En attente',
                            'en_cours'   => 'En cours',
                            'termine'    => 'Terminé',
                            'refuse'     => 'Refusé',
                            default      => $e->getStatut()
                        };
                    ?>
                        <div class="emprunt-card">
                            <div class="emprunt-icon">
                                <i class="bi bi-<?= $mat && $mat->getImage() ? 'tools' : 'tools' ?>"></i>
                            </div>
                            <div class="emprunt-info">
                                <div class="emprunt-title">
                                    <?= $mat ? htmlspecialchars($mat->getNomMateriel()) : 'Matériel supprimé' ?>
                                </div>
                                <div class="emprunt-dates">
                                    <span><i class="bi bi-calendar-plus"></i> Demandé le <?= $e->getDateDemande() ?></span>
                                    <?php if ($e->getDateDebut()): ?>
                                        <span><i class="bi bi-calendar-check"></i> Début : <?= $e->getDateDebut() ?></span>
                                    <?php endif; ?>
                                    <?php if ($e->getDateFinPrevue()): ?>
                                        <span><i class="bi bi-calendar-x"></i> Fin prévue : <?= $e->getDateFinPrevue() ?></span>
                                    <?php endif; ?>
                                    <?php if ($e->getDateRetourEffectif()): ?>
                                        <span><i class="bi bi-check-circle"></i> Retourné le <?= $e->getDateRetourEffectif() ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="emprunt-status">
                                <span class="status-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                <?php if ($mat && $e->getStatut() === 'en_attente'): ?>
                                    <small class="text-muted d-block mt-1">En attente du prêteur</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <!-- ===== ONGLET : MES OUTILS ===== -->
        <?php elseif ($onglet === 'outils'): ?>

            <?php if (empty($mesMateriels)): ?>
                <div class="empty-state">
                    <i class="bi bi-tools"></i>
                    <p>Vous n'avez encore mis aucun outil en ligne.</p>
                    <a href="index.php?page=profil&onglet=ajouter" class="btn-primary-sm">Proposer un outil</a>
                </div>
            <?php else: ?>
                <div class="outils-grid">
                    <?php foreach ($mesMateriels as $m):
                        $cat = $categorieDAO->getById($m->getIdCategorie());
                    ?>
                        <div class="outil-card <?= $m->isDisponible() ? '' : 'outil-indispo' ?>">
                            <div class="outil-card-img">
                                <?php if ($m->getImage()): ?>
                                    <img src="public/img/<?= htmlspecialchars($m->getImage()) ?>"
                                         alt="<?= htmlspecialchars($m->getNomMateriel()) ?>">
                                <?php else: ?>
                                    <i class="bi bi-tools"></i>
                                <?php endif; ?>
                            </div>
                            <div class="outil-card-body">
                                <div class="outil-card-header">
                                    <h3><?= htmlspecialchars($m->getNomMateriel()) ?></h3>
                                    <span class="<?= $m->isDisponible() ? 'badge-green' : 'badge-red' ?> badge">
                                        <?= $m->isDisponible() ? 'Disponible' : 'Indisponible' ?>
                                    </span>
                                </div>
                                <p class="outil-desc"><?= htmlspecialchars($m->getCaracteristique()) ?></p>
                                <div class="outil-meta">
                                    <?php if ($cat): ?>
                                        <span><i class="bi bi-tag"></i> <?= htmlspecialchars($cat->getNomCategorie()) ?></span>
                                    <?php endif; ?>
                                    <span><i class="bi bi-star"></i> <?= htmlspecialchars($m->getEtatLabel()) ?></span>
                                    <span><i class="bi bi-clock"></i> <?= $m->getDureePretMax() ?> jours max</span>
                                </div>
                            </div>
                            <div class="outil-card-footer">
                                <a href="index.php?page=materiel&id=<?= $m->getId() ?>" class="btn-view">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <a href="index.php?page=profil&onglet=outils&supprimer_mat=<?= $m->getId() ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Supprimer cet outil ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <!-- ===== ONGLET : PROPOSER UN OUTIL ===== -->
        <?php elseif ($onglet === 'ajouter'): ?>

            <div class="add-tool-container">
                <div class="add-tool-header">
                    <span class="section-label">Partager</span>
                    <h2 class="section-title">Proposer un outil</h2>
                    <p class="section-sub">Mettez votre matériel à disposition de la communauté en quelques secondes.</p>
                </div>

                <form method="POST" action="index.php?page=profil&onglet=ajouter" class="add-tool-form">

                    <!-- Étape 1 : Informations -->
                    <div class="form-step">
                        <div class="form-step-label">
                            <span class="step-num">1</span>
                            Informations générales
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="nom_materiel">Nom de l'outil <span class="required">*</span></label>
                                <input type="text" id="nom_materiel" name="nom_materiel"
                                       placeholder="ex : Perceuse Bosch 18V" required
                                       value="<?= htmlspecialchars($_POST['nom_materiel'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="id_categorie">Catégorie <span class="required">*</span></label>
                                <select id="id_categorie" name="id_categorie" required>
                                    <option value="">— Choisir une catégorie —</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat->getId() ?>"
                                            <?= (($_POST['id_categorie'] ?? '') == $cat->getId()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat->getNomCategorie()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="caracteristique">Description et caractéristiques <span class="required">*</span></label>
                            <textarea id="caracteristique" name="caracteristique" rows="3"
                                      placeholder="Décrivez votre outil : marque, modèle, accessoires inclus…" required><?= htmlspecialchars($_POST['caracteristique'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Étape 2 : État et durée -->
                    <div class="form-step">
                        <div class="form-step-label">
                            <span class="step-num">2</span>
                            État et conditions de prêt
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="etat">État de l'outil <span class="required">*</span></label>
                                <select id="etat" name="etat" required>
                                    <?php
                                    $etats = [
                                        'neuf'         => 'Neuf',
                                        'bon_etat'     => 'Bon état',
                                        'etat_correct' => 'État correct',
                                        'abime'        => 'Abîmé',
                                    ];
                                    foreach ($etats as $val => $label): ?>
                                        <option value="<?= $val ?>"
                                            <?= (($_POST['etat'] ?? 'bon_etat') === $val) ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="duree_pret_max">Durée de prêt maximum <span class="required">*</span></label>
                                <div class="input-with-unit">
                                    <input type="number" id="duree_pret_max" name="duree_pret_max"
                                           min="1" max="90" required
                                           value="<?= htmlspecialchars($_POST['duree_pret_max'] ?? '7') ?>">
                                    <span class="unit">jours</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3 : Récapitulatif et soumission -->
                    <div class="form-step form-step-last">
                        <div class="form-step-label">
                            <span class="step-num">3</span>
                            Finalisation
                        </div>
                        <div class="form-checklist">
                            <label class="check-item">
                                <input type="checkbox" required>
                                <span>Je certifie être propriétaire de cet outil et l'avoir décrit fidèlement.</span>
                            </label>
                            <label class="check-item">
                                <input type="checkbox" required>
                                <span>J'accepte de le prêter aux membres de la communauté Eco'Loc.</span>
                            </label>
                        </div>
                        <button type="submit" name="ajouter_materiel" class="btn-submit-tool">
                            <i class="bi bi-send"></i> Mettre en ligne mon outil
                        </button>
                    </div>

                </form>
            </div>

        <?php endif; ?>

    </div><!-- /profil-body -->
</div><!-- /profil-page -->
