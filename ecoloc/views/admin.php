<?php
$auth->requireAdmin();

require_once 'dao/UtilisateurDAO.php';
require_once 'dao/MaterielDAO.php';
require_once 'dao/EmpruntDAO.php';
require_once 'dao/CategorieDAO.php';

$userDAO      = new UtilisateurDAO();
$materielDAO  = new MaterielDAO();
$empruntDAO   = new EmpruntDAO();
$categorieDAO = new CategorieDAO();

$message = '';

// Valider un compte
if (isset($_GET['valider_compte'])) {
    $userDAO->validerCompte((int)$_GET['valider_compte']);
    $message = "✅ Compte validé avec succès.";
}

// Supprimer un utilisateur
if (isset($_GET['supprimer_user'])) {
    $userDAO->delete((int)$_GET['supprimer_user']);
    $message = "✅ Utilisateur supprimé.";
}

// Valider un emprunt
if (isset($_GET['valider_emprunt'])) {
    $empruntDAO->valider((int)$_GET['valider_emprunt']);
    $message = "✅ Emprunt validé.";
}

// Refuser un emprunt
if (isset($_GET['refuser_emprunt'])) {
    $empruntDAO->refuser((int)$_GET['refuser_emprunt']);
    $message = "✅ Emprunt refusé.";
}

// Valider un retour
if (isset($_GET['retour_emprunt'])) {
    $id = (int)$_GET['retour_emprunt'];
    $empruntDAO->retourner($id);
    $e = $empruntDAO->getById($id);
    if ($e) $materielDAO->setDisponible($e->getIdMateriel(), true);
    $message = "✅ Retour validé.";
}

// Ajouter une catégorie
if (isset($_POST['nouvelle_categorie']) && !empty(trim($_POST['nouvelle_categorie']))) {
    $categorieDAO->create(htmlspecialchars(trim($_POST['nouvelle_categorie'])));
    $message = "✅ Catégorie ajoutée.";
}

// Supprimer un matériel
if (isset($_GET['supprimer_materiel'])) {
    $materielDAO->delete((int)$_GET['supprimer_materiel']);
    $message = "✅ Matériel supprimé.";
}

// Récupérer les données
$utilisateurs = $userDAO->getAll();
$emprunts     = $empruntDAO->getAll();
$materiels    = $materielDAO->getAll();
$categories   = $categorieDAO->getAll();

$onglet      = $_GET['onglet'] ?? 'dashboard';
$filtreStatut = $_GET['statut'] ?? 'tous';
$enAttente   = array_filter($utilisateurs, fn($u) => $u->getStatutCompte() === 'en_attente');
?>

<div class="container-fluid py-4">

    <h2 class="fw-bold mb-4">
        <i class="bi bi-gear-fill text-primary"></i> Panneau d'administration
    </h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ONGLETS -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $onglet === 'dashboard' ? 'active' : '' ?>"
               href="index.php?page=admin&onglet=dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $onglet === 'comptes' ? 'active' : '' ?>"
               href="index.php?page=admin&onglet=comptes">
                <i class="bi bi-people"></i> Comptes
                <?php if (count($enAttente) > 0): ?>
                    <span class="badge bg-danger ms-1"><?= count($enAttente) ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $onglet === 'emprunts' ? 'active' : '' ?>"
               href="index.php?page=admin&onglet=emprunts">
                <i class="bi bi-arrow-left-right"></i> Emprunts
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $onglet === 'catalogue' ? 'active' : '' ?>"
               href="index.php?page=admin&onglet=catalogue">
                <i class="bi bi-tools"></i> Catalogue
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $onglet === 'categories' ? 'active' : '' ?>"
               href="index.php?page=admin&onglet=categories">
                <i class="bi bi-tags"></i> Catégories
            </a>
        </li>
    </ul>

    <!-- ===== DASHBOARD ===== -->
    <?php if ($onglet === 'dashboard'): ?>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-4">
                    <i class="bi bi-people fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold"><?= count($utilisateurs) ?></h3>
                    <p class="text-muted mb-0">Utilisateurs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-4">
                    <i class="bi bi-tools fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold"><?= count($materiels) ?></h3>
                    <p class="text-muted mb-0">Matériels</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-4">
                    <i class="bi bi-arrow-left-right fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold"><?= count($emprunts) ?></h3>
                    <p class="text-muted mb-0">Emprunts</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-4">
                    <i class="bi bi-tags fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold"><?= count($categories) ?></h3>
                    <p class="text-muted mb-0">Catégories</p>
                </div>
            </div>
        </div>

        <?php if (count($enAttente) > 0): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="bi bi-exclamation-circle"></i>
                <?= count($enAttente) ?> compte(s) en attente de validation
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enAttente as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u->getPrenom() . ' ' . $u->getNom()) ?></td>
                            <td><?= htmlspecialchars($u->getEmail()) ?></td>
                            <td><?= htmlspecialchars($u->getTelephone()) ?></td>
                            <td class="d-flex gap-1">
                                <a href="index.php?page=admin&valider_compte=<?= $u->getId() ?>&onglet=dashboard"
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-check"></i> Valider
                                </a>
                                <a href="index.php?page=admin&supprimer_user=<?= $u->getId() ?>&onglet=dashboard"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Refuser et supprimer ce compte ?')">
                                    <i class="bi bi-x"></i> Refuser
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Derniers emprunts -->
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold bg-white">
                <i class="bi bi-clock-history"></i> Derniers emprunts
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Matériel</th>
                            <th>Propriétaire</th>
                            <th>Emprunteur</th>
                            <th>Date demande</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($emprunts, 0, 5) as $e):
                            $mat        = $materielDAO->getById($e->getIdMateriel());
                            $emprunteur = $userDAO->getById($e->getIdEmprunteur());
                            $proprio    = $mat ? $userDAO->getById($mat->getIdProprietaire()) : null;
                            $badgeClass = match($e->getStatut()) {
                                'en_attente' => 'bg-warning text-dark',
                                'accepte', 'en_cours' => 'bg-success',
                                'refuse'    => 'bg-danger',
                                'termine'   => 'bg-secondary',
                                default     => 'bg-secondary'
                            };
                        ?>
                        <tr>
                            <td class="text-muted">#<?= $e->getId() ?></td>
                            <td><?= $mat ? htmlspecialchars($mat->getNomMateriel()) : 'N/A' ?></td>
                            <td><?= $proprio ? htmlspecialchars($proprio->getPrenom() . ' ' . $proprio->getNom()) : 'N/A' ?></td>
                            <td><?= $emprunteur ? htmlspecialchars($emprunteur->getPrenom() . ' ' . $emprunteur->getNom()) : 'N/A' ?></td>
                            <td><?= $e->getDateDemande() ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $e->getStatut() ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ===== COMPTES ===== -->
    <?php elseif ($onglet === 'comptes'): ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people"></i> Gestion des comptes</span>
                <span class="badge bg-primary"><?= count($utilisateurs) ?> utilisateurs</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Adresse</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td class="text-muted">#<?= $u->getId() ?></td>
                            <td>
                                <strong><?= htmlspecialchars($u->getPrenom() . ' ' . $u->getNom()) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($u->getEmail()) ?></td>
                            <td><?= htmlspecialchars($u->getTelephone()) ?></td>
                            <td><?= htmlspecialchars($u->getAdresse()) ?></td>
                            <td>
                                <span class="badge <?= $u->getRole() === 'admin' ? 'bg-primary' : ($u->getRole() === 'abonne' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                                    <?= $u->getRole() ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $u->getStatutCompte() === 'valide' ? 'bg-success' : ($u->getStatutCompte() === 'en_attente' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                    <?= $u->getStatutCompte() ?>
                                </span>
                            </td>
                            <td class="d-flex gap-1">
                                <?php if ($u->getStatutCompte() === 'en_attente'): ?>
                                    <a href="index.php?page=admin&valider_compte=<?= $u->getId() ?>&onglet=comptes"
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-check"></i> Valider
                                    </a>
                                <?php endif; ?>
                                <?php if ($u->getRole() !== 'admin'): ?>
                                    <a href="index.php?page=admin&supprimer_user=<?= $u->getId() ?>&onglet=comptes"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Supprimer cet utilisateur ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ===== EMPRUNTS ===== -->
    <?php elseif ($onglet === 'emprunts'): ?>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <?php
            $statuts = ['tous', 'en_attente', 'en_cours', 'termine', 'refuse'];
            $labels  = [
                'tous'       => '🔵 Tous',
                'en_attente' => '🟡 En attente',
                'en_cours'   => '🟢 En cours',
                'termine'    => '⚫ Terminés',
                'refuse'     => '🔴 Refusés'
            ];
            foreach ($statuts as $s):
            ?>
                <a href="index.php?page=admin&onglet=emprunts&statut=<?= $s ?>"
                   class="btn btn-sm <?= $filtreStatut === $s ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <?= $labels[$s] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-arrow-left-right"></i> Historique des emprunts</span>
                <span class="badge bg-primary"><?= count($emprunts) ?> au total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Matériel</th>
                                <th>Propriétaire</th>
                                <th>Emprunteur</th>
                                <th>Date demande</th>
                                <th>Date début</th>
                                <th>Retour prévu</th>
                                <th>Retour effectif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $empruntsAffiches = $emprunts;
                            if ($filtreStatut !== 'tous') {
                                $empruntsAffiches = array_filter($emprunts, fn($e) => $e->getStatut() === $filtreStatut);
                            }
                            if (empty($empruntsAffiches)):
                            ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        Aucun emprunt trouvé.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($empruntsAffiches as $e):
                                    $mat        = $materielDAO->getById($e->getIdMateriel());
                                    $emprunteur = $userDAO->getById($e->getIdEmprunteur());
                                    $proprio    = $mat ? $userDAO->getById($mat->getIdProprietaire()) : null;
                                    $badgeClass = match($e->getStatut()) {
                                        'en_attente' => 'bg-warning text-dark',
                                        'accepte', 'en_cours' => 'bg-success',
                                        'refuse'    => 'bg-danger',
                                        'termine'   => 'bg-secondary',
                                        default     => 'bg-secondary'
                                    };
                                ?>
                                <tr>
                                    <td class="text-muted">#<?= $e->getId() ?></td>
                                    <td>
                                        <strong><?= $mat ? htmlspecialchars($mat->getNomMateriel()) : 'N/A' ?></strong>
                                        <?php if ($mat): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($mat->getEtatLabel()) ?> —
                                                <?= $mat->getDureePretMax() ?> jours max
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($proprio): ?>
                                            <i class="bi bi-person-fill text-primary"></i>
                                            <?= htmlspecialchars($proprio->getPrenom() . ' ' . $proprio->getNom()) ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($proprio->getEmail()) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($emprunteur): ?>
                                            <i class="bi bi-person text-success"></i>
                                            <?= htmlspecialchars($emprunteur->getPrenom() . ' ' . $emprunteur->getNom()) ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($emprunteur->getEmail()) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $e->getDateDemande() ?></td>
                                    <td><?= $e->getDateDebut() ?? '<span class="text-muted">—</span>' ?></td>
                                    <td><?= $e->getDateFinPrevue() ?? '<span class="text-muted">—</span>' ?></td>
                                    <td>
                                        <?php if ($e->getDateRetourEffectif()): ?>
                                            <span class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                <?= $e->getDateRetourEffectif() ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $e->getStatut() ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if ($e->getStatut() === 'en_attente'): ?>
                                                <a href="index.php?page=admin&valider_emprunt=<?= $e->getId() ?>&onglet=emprunts&statut=<?= $filtreStatut ?>"
                                                   class="btn btn-success btn-sm" title="Valider">
                                                    <i class="bi bi-check-lg"></i>
                                                </a>
                                                <a href="index.php?page=admin&refuser_emprunt=<?= $e->getId() ?>&onglet=emprunts&statut=<?= $filtreStatut ?>"
                                                   class="btn btn-danger btn-sm" title="Refuser"
                                                   onclick="return confirm('Refuser cet emprunt ?')">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            <?php elseif ($e->getStatut() === 'en_cours'): ?>
                                                <a href="index.php?page=admin&retour_emprunt=<?= $e->getId() ?>&onglet=emprunts&statut=<?= $filtreStatut ?>"
                                                   class="btn btn-primary btn-sm"
                                                   onclick="return confirm('Confirmer le retour ?')">
                                                    <i class="bi bi-arrow-return-left"></i> Retour
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- ===== CATALOGUE ===== -->
    <?php elseif ($onglet === 'catalogue'): ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-tools"></i> Gestion du catalogue</span>
                <span class="badge bg-primary"><?= count($materiels) ?> matériels</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Caractéristique</th>
                            <th>Catégorie</th>
                            <th>Propriétaire</th>
                            <th>État</th>
                            <th>Disponible</th>
                            <th>Durée max</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiels as $m):
                            $proprio = $userDAO->getById($m->getIdProprietaire());
                            $cat     = $categorieDAO->getById($m->getIdCategorie());
                        ?>
                        <tr>
                            <td class="text-muted">#<?= $m->getId() ?></td>
                            <td><strong><?= htmlspecialchars($m->getNomMateriel()) ?></strong></td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($m->getCaracteristique()) ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= $cat ? htmlspecialchars($cat->getNomCategorie()) : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <?= $proprio ? htmlspecialchars($proprio->getPrenom() . ' ' . $proprio->getNom()) : 'N/A' ?>
                            </td>
                            <td><?= htmlspecialchars($m->getEtatLabel()) ?></td>
                            <td>
                                <span class="badge <?= $m->isDisponible() ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $m->isDisponible() ? 'Oui' : 'Non' ?>
                                </span>
                            </td>
                            <td><?= $m->getDureePretMax() ?> jours</td>
                            <td>
                                <a href="index.php?page=admin&supprimer_materiel=<?= $m->getId() ?>&onglet=catalogue"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer ce matériel ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ===== CATEGORIES ===== -->
    <?php elseif ($onglet === 'categories'): ?>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header fw-bold bg-white">
                        <i class="bi bi-plus-circle"></i> Ajouter une catégorie
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php?page=admin&onglet=categories">
                            <div class="input-group">
                                <input type="text" name="nouvelle_categorie"
                                       class="form-control"
                                       placeholder="Nom de la catégorie" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus"></i> Ajouter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header fw-bold bg-white">
                        <i class="bi bi-tags"></i> Catégories existantes
                        <span class="badge bg-primary ms-2"><?= count($categories) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Nb matériels</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c):
                                    $nbMat = count(array_filter($materiels, fn($m) => $m->getIdCategorie() === $c->getId()));
                                ?>
                                <tr>
                                    <td class="text-muted">#<?= $c->getId() ?></td>
                                    <td><strong><?= htmlspecialchars($c->getNomCategorie()) ?></strong></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $nbMat ?> matériel(s)</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>