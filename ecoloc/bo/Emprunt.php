<?php
class Emprunt {
    private int $id;
    private string $dateDemande;
    private ?string $dateDebut;
    private ?string $dateFinPrevue;
    private ?string $dateRetourEffectif;
    private string $statut;
    private int $idEmprunteur;
    private int $idMateriel;

    public function __construct(
        int $id = 0,
        string $dateDemande = '',
        ?string $dateDebut = null,
        ?string $dateFinPrevue = null,
        ?string $dateRetourEffectif = null,
        string $statut = 'en_attente',
        int $idEmprunteur = 0,
        int $idMateriel = 0
    ) {
        $this->id = $id;
        $this->dateDemande = $dateDemande;
        $this->dateDebut = $dateDebut;
        $this->dateFinPrevue = $dateFinPrevue;
        $this->dateRetourEffectif = $dateRetourEffectif;
        $this->statut = $statut;
        $this->idEmprunteur = $idEmprunteur;
        $this->idMateriel = $idMateriel;
    }
    

    //get set
    public function getId(): int { return $this->id; }
    public function getDateDemande(): string { return $this->dateDemande; }
    public function getDateDebut(): ?string { return $this->dateDebut; }
    public function getDateFinPrevue(): ?string { return $this->dateFinPrevue; }
    public function getDateRetourEffectif(): ?string { return $this->dateRetourEffectif; }
    public function getStatut(): string { return $this->statut; }
    public function getIdEmprunteur(): int { return $this->idEmprunteur; }
    public function getIdMateriel(): int { return $this->idMateriel; }

    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setDateDebut(string $date): void { $this->dateDebut = $date; }
    public function setDateFinPrevue(string $date): void { $this->dateFinPrevue = $date; }
    public function setDateRetourEffectif(string $date): void { $this->dateRetourEffectif = $date; }
}