<?php
session_start();
require_once 'controllers/AuthController.php';

$auth = new AuthController();
$page = $_GET['page'] ?? 'accueil';
$erreur = '';
$errors = [];
$success = false;

// Traitement connexion
if (isset($_POST['connexion'])) {
    $email = $_POST['email'] ?? '';
    $mdp   = $_POST['mot_de_passe'] ?? '';
    if ($auth->login($email, $mdp)) {
        header('Location: index.php?page=catalogue');
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect, ou compte non validé.";
        $page = 'login';
    }
}

// Traitement inscription
if (isset($_POST['inscription'])) {
    $errors = $auth->register($_POST);
    if (empty($errors)) {
        $success = true;
    }
    $page = 'register';
}

// Traitement déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth->logout();
}

// Chargement de la vue
include 'views/layout.php';