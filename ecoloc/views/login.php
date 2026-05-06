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
                    Partagez vos outils,<br>enrichissez votre quartier.
                </h2>
                <ul class="auth-features">
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-tools"></i></span>
                        <span>+14 outils disponibles dans le catalogue</span>
                    </li>
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-clock-history"></i></span>
                        <span>Demandes traitées en moins de 24h</span>
                    </li>
                    <li>
                        <span class="auth-feat-icon"><i class="bi bi-people"></i></span>
                        <span>Communauté locale de confiance</span>
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

                <?php if (!empty($erreur)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <div class="auth-form-header">
                    <h2>Bon retour !</h2>
                    <p>Connectez-vous pour accéder à votre compte ECO'LOC.</p>
                </div>

                <form method="POST" action="index.php?page=login" novalidate>
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" id="email" name="email"
                                   placeholder="vous@exemple.fr" required
                                   autocomplete="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" id="mot_de_passe" name="mot_de_passe"
                                   placeholder="••••••••" required
                                   autocomplete="current-password">
                        </div>
                    </div>
                    <button type="submit" name="connexion" class="btn-auth-submit">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </button>
                </form>

                <div class="auth-form-footer">
                    <p>Pas encore de compte ?
                        <a href="index.php?page=register">Créer un compte</a>
                    </p>
                    <a href="index.php?page=catalogue" class="auth-guest-link">
                        <i class="bi bi-eye"></i> Voir le catalogue sans se connecter
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
