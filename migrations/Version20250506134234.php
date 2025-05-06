<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506134234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "Match" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, equipe1_id INTEGER NOT NULL, equipe2_id INTEGER NOT NULL, poule_id INTEGER NOT NULL, date_match DATE NOT NULL, heur_debut TIME NOT NULL, heur_fin TIME NOT NULL, durée_pause INTEGER NOT NULL, match_en_cours BOOLEAN NOT NULL, score1 INTEGER NOT NULL, score2 INTEGER NOT NULL, CONSTRAINT FK_BB9AEA014265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0150D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0126596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB9AEA014265900C ON "Match" (equipe1_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB9AEA0150D03FE2 ON "Match" (equipe2_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB9AEA0126596FD8 ON "Match" (poule_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Terrain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, occupé BOOLEAN NOT NULL, longueur INTEGER DEFAULT NULL, largeur INTEGER DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Tournoi (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_tournoi DATE NOT NULL, intitule VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , mot_de_passe VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, mail VARCHAR(255) NOT NULL, numero_de_téléphone VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9B80EC645126AC48 ON Utilisateur (mail)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9B80EC64CF70645 ON Utilisateur (numero_de_téléphone)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON Utilisateur (login)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tableau_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL, CONSTRAINT FK_2449BA15B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA15B062D5BC ON equipe (tableau_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe_utilisateur (equipe_id INTEGER NOT NULL, utilisateur_id INTEGER NOT NULL, PRIMARY KEY(equipe_id, utilisateur_id), CONSTRAINT FK_D78C92636D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D78C9263FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D78C92636D861B89 ON equipe_utilisateur (equipe_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D78C9263FB88E14F ON equipe_utilisateur (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE poule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tableau_id INTEGER DEFAULT NULL, numero INTEGER DEFAULT NULL, CONSTRAINT FK_FA1FEB40B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FA1FEB40B062D5BC ON poule (tableau_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tableau (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tournoi_id INTEGER NOT NULL, intitule VARCHAR(255) NOT NULL, niveau INTEGER NOT NULL, age INTEGER NOT NULL, sexe VARCHAR(255) NOT NULL, CONSTRAINT FK_C6744DB1F607770A FOREIGN KEY (tournoi_id) REFERENCES Tournoi (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C6744DB1F607770A ON tableau (tournoi_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE "Match"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Terrain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Tournoi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe_utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poule
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tableau
        SQL);
    }
}
