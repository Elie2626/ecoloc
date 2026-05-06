<?php
require_once 'config/db.php';
require_once 'bo/Categorie.php';

class CategorieDAO {

    public function getAll(): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM categorie ORDER BY nom_categorie");
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new Categorie($row['id_categorie'], $row['nom_categorie']);
        }
        return $result;
    }

    public function getById(int $id): ?Categorie {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM categorie WHERE id_categorie=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Categorie($row['id_categorie'], $row['nom_categorie']);
    }

    public function create(string $nom): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("INSERT INTO categorie (nom_categorie) VALUES (:nom)");
        return $stmt->execute([':nom' => $nom]);
    }

    public function delete(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("DELETE FROM categorie WHERE id_categorie=:id");
        return $stmt->execute([':id' => $id]);
    }
}