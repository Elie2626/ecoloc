<?php
require_once 'dao/UtilisateurDAO.php';

class AuthController {

    public function login(string $email, string $mdp): bool {
        // Sécurité : on nettoie l'email
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $dao = new UtilisateurDAO();
        $user = $dao->getByEmail($email);

        if (!$user) return false;

        // Vérification compte validé
        if ($user->getStatutCompte() !== 'valide') return false;

        // Vérification mot de passe
        if (!password_verify($mdp, $user->getMotDePasse())) return false;

        // Création de la session
        session_regenerate_id(true); // protection fixation de session
        $_SESSION['user_id']    = $user->getId();
        $_SESSION['user_nom']   = $user->getNom();
        $_SESSION['user_prenom']= $user->getPrenom();
        $_SESSION['user_role']  = $user->getRole();
        $_SESSION['user_email'] = $user->getEmail();

        return true;
    }

    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function isConnected(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function isAbonne(): bool {
        return isset($_SESSION['user_role']) && 
               in_array($_SESSION['user_role'], ['abonne', 'admin']);
    }

    public function requireLogin(): void {
        if (!$this->isConnected()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function requireAdmin(): void {
        if (!$this->isAdmin()) {
            header('Location: index.php?page=catalogue');
            exit;
        }
    }

    public function requireAbonne(): void {
        if (!$this->isAbonne()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function register(array $data): array {
        $errors = [];

        // Validation
        if (empty(trim($data['nom'])))    $errors[] = "Le nom est obligatoire.";
        if (empty(trim($data['prenom']))) $errors[] = "Le prénom est obligatoire.";
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
            $errors[] = "Email invalide.";
        if (strlen($data['mot_de_passe']) < 6) 
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        if ($data['mot_de_passe'] !== $data['confirm_mdp']) 
            $errors[] = "Les mots de passe ne correspondent pas.";

        if (!empty($errors)) return $errors;

        // Vérifier si email déjà utilisé
        $dao = new UtilisateurDAO();
        if ($dao->getByEmail($data['email'])) {
            $errors[] = "Cet email est déjà utilisé.";
            return $errors;
        }

        // Créer l'utilisateur
        $user = new Utilisateur(
            0,
            htmlspecialchars(trim($data['nom'])),
            htmlspecialchars(trim($data['prenom'])),
            htmlspecialchars(trim($data['telephone'] ?? '')),
            htmlspecialchars(trim($data['adresse'] ?? '')),
            trim($data['email']),
            $data['mot_de_passe'], // sera hashé dans le DAO
            'abonne',
            'en_attente'
        );

        $dao->create($user);
        return [];
    }
}