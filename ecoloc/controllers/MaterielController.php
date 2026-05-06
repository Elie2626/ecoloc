<?php
require_once 'dao/MaterielDAO.php';
require_once 'dao/CategorieDAO.php';
require_once 'dao/EmpruntDAO.php';

class MaterielController {

    private MaterielDAO $materielDAO;
    private CategorieDAO $categorieDAO;
    private EmpruntDAO $empruntDAO;

    public function __construct() {
        $this->materielDAO  = new MaterielDAO();
        $this->categorieDAO = new CategorieDAO();
        $this->empruntDAO   = new EmpruntDAO();
    }

    public function getCatalogue(string $motcle = '', int $idCategorie = 0): array {
        if (!empty($motcle) || $idCategorie > 0) {
            return $this->materielDAO->search($motcle, $idCategorie);
        }
        return $this->materielDAO->getAll();
    }
    public function getCategories(): array {
        return $this->categorieDAO->getAll();
    }
    public function getMateriel(int $id): ?Materiel {
        return $this->materielDAO->getById($id);
    }
    public function demanderEmprunt(int $idMateriel, int $idEmprunteur): string {
        // Vérifier disponibilité
        if (!$this->empruntDAO->isMaterielDisponible($idMateriel)) {
            return "Ce matériel est déjà en cours d'emprunt.";
        }
        $emprunt = new Emprunt(0, date('Y-m-d'), null, null, null, 'en_attente', $idEmprunteur, $idMateriel);
        $this->empruntDAO->create($emprunt);
        $this->materielDAO->setDisponible($idMateriel, false);
        return "success";
    }
    public function getMaterielParProprietaire(int $idUser): array {
        return $this->materielDAO->getByProprietaire($idUser);
    }
    

    
    public function ajouterMateriel(array $data, int $idProprietaire): bool {
        $m = new Materiel(
            0,
            htmlspecialchars(trim($data['nom_materiel'])),
            htmlspecialchars(trim($data['caracteristique'])),
            (int)$data['duree_pret_max'],
            true,
            $data['etat'],
            $idProprietaire,
            (int)$data['id_categorie']
        );
        return $this->materielDAO->create($m);
    }

    public function supprimerMateriel(int $id): bool {
        return $this->materielDAO->delete($id);
    }
}