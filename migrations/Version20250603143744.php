<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603143744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__tableau AS SELECT id, tournoi_id, intitule, niveau, age, sexe FROM tableau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tableau
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tableau (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tournoi_id INTEGER NOT NULL, intitule VARCHAR(255) NOT NULL, niveau DOUBLE PRECISION NOT NULL, age INTEGER NOT NULL, sexe VARCHAR(255) NOT NULL, CONSTRAINT FK_C6744DB1F607770A FOREIGN KEY (tournoi_id) REFERENCES Tournoi (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO tableau (id, tournoi_id, intitule, niveau, age, sexe) SELECT id, tournoi_id, intitule, niveau, age, sexe FROM __temp__tableau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__tableau
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C6744DB1F607770A ON tableau (tournoi_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__tableau AS SELECT id, tournoi_id, intitule, niveau, age, sexe FROM tableau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tableau
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tableau (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tournoi_id INTEGER NOT NULL, intitule VARCHAR(255) NOT NULL, niveau INTEGER NOT NULL, age INTEGER NOT NULL, sexe VARCHAR(255) NOT NULL, CONSTRAINT FK_C6744DB1F607770A FOREIGN KEY (tournoi_id) REFERENCES Tournoi (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO tableau (id, tournoi_id, intitule, niveau, age, sexe) SELECT id, tournoi_id, intitule, niveau, age, sexe FROM __temp__tableau
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__tableau
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C6744DB1F607770A ON tableau (tournoi_id)
        SQL);
    }
}
