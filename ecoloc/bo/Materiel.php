<?php
class Materiel {
    private int $id;
    private string $nomMateriel;
    private string $caracteristique;
    private int $dureePretMax;
    private bool $disponible;
    private string $etat;
    private int $idProprietaire;
    private int $idCategorie;
    private ?string $image;

    public function __construct(
        int $id = 0,
        string $nomMateriel = '',
        string $caracteristique = '',
        int $dureePretMax = 0,
        bool $disponible = true,
        string $etat = 'neuf',
        int $idProprietaire = 0,
        int $idCategorie = 0,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->nomMateriel = $nomMateriel;
        $this->caracteristique = $caracteristique;
        $this->dureePretMax = $dureePretMax;
        $this->disponible = $disponible;
        $this->etat = $etat;
        $this->idProprietaire = $idProprietaire;
        $this->idCategorie = $idCategorie;
        $this->image = $image;
    }

    public function getId(): int { return $this->id; }
    public function getNomMateriel(): string { return $this->nomMateriel; }
    public function getCaracteristique(): string { return $this->caracteristique; }
    public function getDureePretMax(): int { return $this->dureePretMax; }
    public function isDisponible(): bool { return $this->disponible; }
    public function getEtat(): string { return $this->etat; }
    public function getIdProprietaire(): int { return $this->idProprietaire; }
    public function getIdCategorie(): int { return $this->idCategorie; }
    public function getImage(): ?string { return $this->image; }

    public function getEtatLabel(): string {
        return match($this->etat) {
            'neuf'         => 'Neuf',
            'bon_etat'     => 'Bon état',
            'etat_correct' => 'État correct',
            'abime'        => 'Abîmé',
            default        => ucfirst($this->etat)
        };
    }

    public function setNomMateriel(string $nom): void { $this->nomMateriel = $nom; }
    public function setCaracteristique(string $c): void { $this->caracteristique = $c; }
    public function setDureePretMax(int $d): void { $this->dureePretMax = $d; }
    public function setDisponible(bool $d): void { $this->disponible = $d; }
    public function setEtat(string $e): void { $this->etat = $e; }
    public function setIdCategorie(int $id): void { $this->idCategorie = $id; }
    public function setImage(?string $img): void { $this->image = $img; }
}