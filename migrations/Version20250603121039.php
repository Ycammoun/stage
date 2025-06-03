<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603121039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__equipe AS SELECT id, tableau_id, poule_id, nom FROM equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tableau_id INTEGER DEFAULT NULL, poule_id INTEGER DEFAULT NULL, tournoi_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL, CONSTRAINT FK_2449BA15B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2449BA1526596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2449BA15F607770A FOREIGN KEY (tournoi_id) REFERENCES Tournoi (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO equipe (id, tableau_id, poule_id, nom) SELECT id, tableau_id, poule_id, nom FROM __temp__equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA1526596FD8 ON equipe (poule_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA15B062D5BC ON equipe (tableau_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA15F607770A ON equipe (tournoi_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__equipe AS SELECT id, tableau_id, poule_id, nom FROM equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tableau_id INTEGER DEFAULT NULL, poule_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL, CONSTRAINT FK_2449BA15B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2449BA1526596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO equipe (id, tableau_id, poule_id, nom) SELECT id, tableau_id, poule_id, nom FROM __temp__equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA15B062D5BC ON equipe (tableau_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA1526596FD8 ON equipe (poule_id)
        SQL);
    }
}
