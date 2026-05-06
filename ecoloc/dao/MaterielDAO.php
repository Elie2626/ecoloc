<?php
require_once 'config/db.php';
require_once 'bo/Materiel.php';

class MaterielDAO {

    public function getAll(): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM materiel");
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToMateriel($row);
        }
        return $result;
    }

    public function getById(int $id): ?Materiel {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM materiel WHERE id_materiel=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return $this->rowToMateriel($row);
    }

    public function getByProprietaire(int $idUser): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM materiel WHERE id_proprietaire=:id");
        $stmt->execute([':id' => $idUser]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToMateriel($row);
        }
        return $result;
    }

    public function search(string $motcle, int $idCategorie = 0): array {
        $pdo = getConnexion();
        $sql = "SELECT * FROM materiel WHERE nom_materiel LIKE :motcle";
        $params = [':motcle' => '%' . $motcle . '%'];
        if ($idCategorie > 0) {
            $sql .= " AND id_categorie = :cat";
            $params[':cat'] = $idCategorie;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToMateriel($row);
        }
        return $result;
    }

    public function isDisponible(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT disponible FROM materiel WHERE id_materiel=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row && $row['disponible'] == 1;
    }

    public function create(Materiel $m): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO materiel (nom_materiel, caracteristique, duree_pret_max, disponible, etat, id_proprietaire, id_categorie, image)
            VALUES (:nom, :carac, :duree, :dispo, :etat, :proprio, :cat, :image)
        ");
        return $stmt->execute([
            ':nom'    => $m->getNomMateriel(),
            ':carac'  => $m->getCaracteristique(),
            ':duree'  => $m->getDureePretMax(),
            ':dispo'  => $m->isDisponible() ? 1 : 0,
            ':etat'   => $m->getEtat(),
            ':proprio'=> $m->getIdProprietaire(),
            ':cat'    => $m->getIdCategorie(),
            ':image'  => $m->getImage()
        ]);
    }

    public function update(Materiel $m): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            UPDATE materiel SET nom_materiel=:nom, caracteristique=:carac,
            duree_pret_max=:duree, disponible=:dispo, etat=:etat,
            id_categorie=:cat, image=:image
            WHERE id_materiel=:id
        ");
        return $stmt->execute([
            ':nom'  => $m->getNomMateriel(),
            ':carac'=> $m->getCaracteristique(),
            ':duree'=> $m->getDureePretMax(),
            ':dispo'=> $m->isDisponible() ? 1 : 0,
            ':etat' => $m->getEtat(),
            ':cat'  => $m->getIdCategorie(),
            ':image'=> $m->getImage(),
            ':id'   => $m->getId()
        ]);
    }

    public function setDisponible(int $id, bool $dispo): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("UPDATE materiel SET disponible=:dispo WHERE id_materiel=:id");
        return $stmt->execute([':dispo' => $dispo ? 1 : 0, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("DELETE FROM materiel WHERE id_materiel=:id");
        return $stmt->execute([':id' => $id]);
    }

    private function rowToMateriel(array $row): Materiel {
        return new Materiel(
            $row['id_materiel'],
            $row['nom_materiel'],
            $row['caracteristique'],
            $row['duree_pret_max'],
            (bool)$row['disponible'],
            $row['etat'],
            $row['id_proprietaire'],
            $row['id_categorie'],
            $row['image'] ?? null
        );
    }
}