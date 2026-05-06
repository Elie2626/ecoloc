<?php
class Utilisateur {
    private int $id;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $adresse;
    private string $email;
    private string $motDePasse;
    private string $role;
    private string $statutCompte;

    public function __construct(
        int $id = 0,
        string $nom = '',
        string $prenom = '',
        string $telephone = '',
        string $adresse = '',
        string $email = '',
        string $motDePasse = '',
        string $role = 'visiteur',
        string $statutCompte = 'en_attente'
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->statutCompte = $statutCompte;
    }

    // Getteurs
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getTelephone(): string { return $this->telephone; }
    public function getAdresse(): string { return $this->adresse; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->motDePasse; }
    public function getRole(): string { return $this->role; }
    public function getStatutCompte(): string { return $this->statutCompte; }

    // Setteurs
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setTelephone(string $tel): void { $this->telephone = $tel; }
    public function setAdresse(string $adr): void { $this->adresse = $adr; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $mdp): void { $this->motDePasse = $mdp; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setStatutCompte(string $statut): void { $this->statutCompte = $statut; }
}