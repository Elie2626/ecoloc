<?php
require_once 'config/db.php';
require_once 'bo/Emprunt.php';

class EmpruntDAO {

    public function getAll(): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM emprunt ORDER BY date_demande DESC");
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToEmprunt($row);
        }
        return $result;
    }

    public function getById(int $id): ?Emprunt {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM emprunt WHERE id_emprunt=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return $this->rowToEmprunt($row);
    }

    public function getByEmprunteur(int $idUser): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM emprunt WHERE id_emprunteur=:id ORDER BY date_demande DESC");
        $stmt->execute([':id' => $idUser]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToEmprunt($row);
        }
        return $result;
    }

    public function getByMateriel(int $idMateriel): array {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM emprunt WHERE id_materiel=:id");
        $stmt->execute([':id' => $idMateriel]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = $this->rowToEmprunt($row);
        }
        return $result;
    }

    // Vérifie si le matériel est déjà en emprunt en cours
    public function isMaterielDisponible(int $idMateriel): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as nb FROM emprunt 
            WHERE id_materiel=:id 
            AND statut IN ('en_attente', 'accepte', 'en_cours')
        ");
        $stmt->execute([':id' => $idMateriel]);
        $row = $stmt->fetch();
        return $row['nb'] == 0;
    }

    public function create(Emprunt $e): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO emprunt (date_demande, statut, id_emprunteur, id_materiel)
            VALUES (:date, :statut, :emprunteur, :materiel)
        ");
        return $stmt->execute([
            ':date'       => date('Y-m-d'),
            ':statut'     => 'en_attente',
            ':emprunteur' => $e->getIdEmprunteur(),
            ':materiel'   => $e->getIdMateriel()
        ]);
    }

    public function valider(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            UPDATE emprunt SET statut='en_cours', date_debut=:date
            WHERE id_emprunt=:id
        ");
        return $stmt->execute([':date' => date('Y-m-d'), ':id' => $id]);
    }

    public function refuser(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("UPDATE emprunt SET statut='refuse' WHERE id_emprunt=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function retourner(int $id): bool {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("
            UPDATE emprunt SET statut='termine', date_retour_effectif=:date
            WHERE id_emprunt=:id
        ");
        return $stmt->execute([':date' => date('Y-m-d'), ':id' => $id]);
    }

    private function rowToEmprunt(array $row): Emprunt {
        return new Emprunt(
            $row['id_emprunt'],
            $row['date_demande'],
            $row['date_debut'],
            $row['date_fin_prevue'],
            $row['date_retour_effectif'],
            $row['statut'],
            $row['id_emprunteur'],
            $row['id_materiel']
        );
    }
}