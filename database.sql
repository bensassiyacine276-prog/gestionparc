CREATE DATABASE IF NOT EXISTS parc_informatique;
USE parc_informatique;

CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    service VARCHAR(50)
);

CREATE TABLE localisation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salle VARCHAR(50) NOT NULL,
    bureau VARCHAR(50)
);

CREATE TABLE equipement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50),
    modele VARCHAR(100),
    num_serie VARCHAR(100),
    date_achat DATE,
    prix DECIMAL(10,2),
    id_categorie INT NOT NULL,
    id_utilisateur INT DEFAULT NULL,
    id_localisation INT DEFAULT NULL,
    id_pc INT DEFAULT NULL,
    FOREIGN KEY (id_categorie) REFERENCES categorie(id),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_localisation) REFERENCES localisation(id),
    FOREIGN KEY (id_pc) REFERENCES equipement(id)
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

INSERT INTO categorie (nom) VALUES
('PC / Ordinateur portable'),
('Écran'),
('Clavier & Souris'),
('Imprimante'),
('Composant interne');

INSERT INTO localisation (salle, bureau) VALUES
('Salle 101', 'Bureau A'),
('Salle 102', 'Bureau B'),
('Salle serveur', NULL);

INSERT INTO utilisateur (nom, prenom, service) VALUES
('Dupont', 'Marie', 'Comptabilité'),
('Martin', 'Lucas', 'Informatique');

INSERT INTO admin (login, mot_de_passe) VALUES
('admin', SHA2('admin123', 256));