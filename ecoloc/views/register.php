<div class="auth-page">
    <div class="auth-split">

        <!-- ── Panneau branding (gauche) ─────────────── -->
        <div class="auth-brand">
            <canvas class="shader-bg shader-bg--profil" data-shader aria-hidden="true"></canvas>
            <div class="auth-brand-inner">
                <div class="auth-logo">
                    <i class="bi bi-tools"></i> ECO'LOC
                </div>
                <h2 class="auth-tagline">
                    Rejoignez la<br>communauté locale.
                </h2>
                <ul class="auth-features">
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-shield-check"></i></span>
                        <span>Compte validé par un administrateur</span>
                    </li>
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-tools"></i></span>
                        <span>Accès à +14 outils du catalogue</span>
                    </li>
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Prêtez et empruntez sans frais</span>
                    </li>
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-leaf"></i></span>
                        <span>Circuit court, zéro gaspillage</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ── Formulaire (droite) ────────────────────── -->
        <div class="auth-form-panel">
            <div class="auth-form-inner">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Compte créé ! En attente de validation par un administrateur.
                    </div>
                <?php endif; ?>

                <div class="auth-form-header">
                    <h2>Créer un compte</h2>
                    <p>Rejoignez ECO'LOC et participez au partage local.</p>
                </div>

                <!-- Step progress -->
                <div class="auth-progress">
                    <div class="auth-step-dot active">1</div>
                    <div class="auth-step-line"></div>
                    <div class="auth-step-dot">2</div>
                    <div class="auth-step-line"></div>
                    <div class="auth-step-dot">3</div>
                </div>

                <form method="POST" action="index.php?page=register" novalidate>

                    <!-- Nom + Prénom -->
                    <div class="auth-name-row">
                        <div class="form-group">
                            <label for="nom">Nom <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" id="nom" name="nom"
                                       placeholder="Dupont" required
                                       autocomplete="family-name"
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" id="prenom" name="prenom"
                                       placeholder="Marie" required
                                       autocomplete="given-name"
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Adresse e-mail <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" id="email" name="email"
                                   placeholder="vous@exemple.fr" required
                                   autocomplete="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone"></i>
                            <input type="tel" id="telephone" name="telephone"
                                   placeholder="06 12 34 56 78"
                                   autocomplete="tel"
                                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-geo-alt"></i>
                            <input type="text" id="adresse" name="adresse"
                                   placeholder="12 rue des Lilas, Lyon"
                                   autocomplete="street-address"
                                   value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" id="mot_de_passe" name="mot_de_passe"
                                   placeholder="8 caractères minimum" required
                                   autocomplete="new-password">
                            <button type="button" class="toggle-pw" aria-label="Afficher le mot de passe"
                                    onclick="togglePw('mot_de_passe',this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmer mot de passe -->
                    <div class="form-group">
                        <label for="confirm_mdp">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" id="confirm_mdp" name="confirm_mdp"
                                   placeholder="Répétez le mot de passe" required
                                   autocomplete="new-password">
                            <button type="button" class="toggle-pw" aria-label="Afficher le mot de passe"
                                    onclick="togglePw('confirm_mdp',this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="inscription" class="btn-auth-submit">
                        <i class="bi bi-person-plus"></i> Créer mon compte
                    </button>
                </form>

                <div class="auth-form-footer">
                    <p>Déjà un compte ?
                        <a href="index.php?page=login">Se connecter</a>
                    </p>
                    <a href="index.php?page=catalogue" class="auth-guest-link">
                        <i class="bi bi-eye"></i> Voir le catalogue sans se connecter
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
function togglePw(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
        btn.setAttribute('aria-label', 'Masquer le mot de passe');
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
        btn.setAttribute('aria-label', 'Afficher le mot de passe');
    }
}
</script>
