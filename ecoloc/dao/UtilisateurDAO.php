<?php
require_once 'config/db.php';
require_once 'bo/Utilisateur.php';

class UtilisateurDAO {

    public function getAll(): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur");
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Utilisateur(
                $row['id_utilisateur'],
                $row['nom'],
                $row['prenom'],
                $row['telephone'],
                $row['adresse'],
                $row['email'],
                $row['mot_de_passe'],
                $row['role'],
                $row['statut_compte']
            );
        }
        return $result;
    }

    public function getById(int $id): ?Utilisateur {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Utilisateur(
            $row['id_utilisateur'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            $row['adresse'],
            $row['email'],
            $row['mot_de_passe'],
            $row['role'],
            $row['statut_compte']
        );
    }

    public function getByEmail(string $email): ?Utilisateur {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Utilisateur(
            $row['id_utilisateur'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            $row['adresse'],
            $row['email'],
            $row['mot_de_passe'],
            $row['role'],
            $row['statut_compte']
        );
    }

    public function create(Utilisateur $u): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO utilisateur (nom, prenom, telephone, adresse, email, mot_de_passe, role, statut_compte)
            VALUES (:nom, :prenom, :telephone, :adresse, :email, :mdp, :role, :statut)
        ");
        return $stmt->execute([
            ':nom'      => $u->getNom(),
            ':prenom'   => $u->getPrenom(),
            ':telephone'=> $u->getTelephone(),
            ':adresse'  => $u->getAdresse(),
            ':email'    => $u->getEmail(),
            ':mdp'      => password_hash($u->getMotDePasse(), PASSWORD_BCRYPT),
            ':role'     => $u->getRole(),
            ':statut'   => $u->getStatutCompte()
        ]);
    }

    public function update(Utilisateur $u): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            UPDATE utilisateur SET nom=:nom, prenom=:prenom, telephone=:telephone,
            adresse=:adresse, email=:email, statut_compte=:statut
            WHERE id_utilisateur=:id
        ");
        return $stmt->execute([
            ':nom'      => $u->getNom(),
            ':prenom'   => $u->getPrenom(),
            ':telephone'=> $u->getTelephone(),
            ':adresse'  => $u->getAdresse(),
            ':email'    => $u->getEmail(),
            ':statut'   => $u->getStatutCompte(),
            ':id'       => $u->getId()
        ]);
    }

    public function validerCompte(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("UPDATE utilisateur SET statut_compte='valide' WHERE id_utilisateur=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur=:id");
        return $stmt->execute([':id' => $id]);
    }
}