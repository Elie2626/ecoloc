<?php
class Categorie {
    private int $id;
    private string $nomCategorie;
    

    //constructeur
    public function __construct(int $id = 0, string $nomCategorie = '') {
        $this->id = $id;
        $this->nomCategorie = $nomCategorie;
    }
    
    //getteur setteur
    public function getId(): int { return $this->id; }
    public function getNomCategorie(): string { return $this->nomCategorie; }
    public function setNomCategorie(string $nom): void { $this->nomCategorie = $nom; }
}