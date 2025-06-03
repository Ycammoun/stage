<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603085538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Utilisateur AS SELECT id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft FROM Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , mot_de_passe VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, mail VARCHAR(255) NOT NULL, numero_de_téléphone VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, dupr VARCHAR(255) DEFAULT NULL, fft VARCHAR(255) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO Utilisateur (id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft) SELECT id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft FROM __temp__Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON Utilisateur (login)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9B80EC64CF70645 ON Utilisateur (numero_de_téléphone)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9B80EC645126AC48 ON Utilisateur (mail)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Utilisateur AS SELECT id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft FROM Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , mot_de_passe VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, mail VARCHAR(255) NOT NULL, numero_de_téléphone VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, dupr INTEGER DEFAULT NULL, fft INTEGER DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO Utilisateur (id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft) SELECT id, login, roles, mot_de_passe, nom, prenom, date_naissance, mail, numero_de_téléphone, code_postal, sexe, dupr, fft FROM __temp__Utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__Utilisateur
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
    }
}
