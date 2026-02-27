create database GLPI; 

use GLPI;

create table utilisateurs(
    id int primary key AUTO_INCREMENT,
    nom varchar(50) not null, 
    prenom varchar(50) not null, 
    email varchar(100) not null unique,
    mot_de_passe varchar(255) not null, 
    role enum('technicien', 'administrateur', 'utilisateur') not null);

create table materiels(
    id int primary key AUTO_INCREMENT, 
    nom varchar(100) not null,
    type enum('ordinateur', 'imprimante', 'tablette') not null,
    marque varchar(50) not null,
    modele varchar(50) not null,
    date_achat date not null,
    status enum('en stock', 'en utilisateur', 'en panne') not null,
    id_employe int, 
    FOREIGN key (id_employe) REFERENCES utilisateurs(id));

insert into materiels (nom, type, marque, modele, date_achat, status, id_employe) values 
('PC Bureau', 'ordinateur', 'Dell', 'OptiPlex 7070', '2022-01-15', 'en utilisateur', 1),
('Imprimante Laser', 'imprimante', 'HP', 'LaserJet Pro M404dn', '2021-11-20', 'en stock', 1),
('Tablette de Service', 'tablette', 'Apple', 'iPad Pro 11"', '2023-03-10', 'en utilisation', 1);

create table incidents(
    id int primary key AUTO_INCREMENT, 
    description text not null, 
    date datetime not null,
    status enum('ouvert', 'en cours', 'fermé') not null,
    id_utilisateur int,
    id_technicien int,
    id_materiel int,
    FOREIGN key (id_materiel) REFERENCES materiels(id),
    FOREIGN key (id_utilisateur) REFERENCES utilisateurs(id),
    FOREIGN key (id_technicien) REFERENCES utilisateurs(id));

create table fournisseurs (
    id int primary key auto_increment,
    nom varchar(100) not null,
    adresse varchar(255) not null,
    contact varchar(100) not null
);