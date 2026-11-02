CREATE TABLE Joueur (
    id_joueur INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE Brute (
    id_brute INT AUTO_INCREMENT PRIMARY KEY,
    id_joueur INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    avatar VARCHAR(255),
    points_de_vie INT NOT NULL,
    force_brute INT NOT NULL,
    defense INT NOT NULL,
    FOREIGN KEY (id_joueur) REFERENCES Joueur(id_joueur) ON DELETE CASCADE
);

CREATE TABLE Defi (
    id_defi INT AUTO_INCREMENT PRIMARY KEY,
    id_brute_auteur INT NOT NULL,
    titre VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    date_creation DATETIME NOT NULL,
    etat_defi INT NOT NULL,
    FOREIGN KEY (id_brute_auteur) REFERENCES Brute(id_brute) ON DELETE CASCADE
);

CREATE TABLE Candidature (
    id_candidature INT AUTO_INCREMENT PRIMARY KEY,
    id_defi INT NOT NULL,
    id_brute_adv INT NOT NULL,
    message VARCHAR(255),
    date_candidature DATETIME NOT NULL,
    etat_candidature INT NOT NULL,
    FOREIGN KEY (id_defi) REFERENCES Defi(id_defi) ON DELETE CASCADE,
    FOREIGN KEY (id_brute_adv) REFERENCES Brute(id_brute) ON DELETE CASCADE
);

CREATE TABLE Combat (
    id_combat INT AUTO_INCREMENT PRIMARY KEY,
    id_defi INT NOT NULL,
    id_brute_vainqueur INT NOT NULL,
    date_combat DATETIME NOT NULL,
    compte_rendu TEXT NOT NULL,
    FOREIGN KEY (id_defi) REFERENCES Defi(id_defi) ON DELETE CASCADE,
    FOREIGN KEY (id_brute_vainqueur) REFERENCES Brute(id_brute) ON DELETE CASCADE
);