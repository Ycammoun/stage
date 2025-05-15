<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514132616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE Terrain ADD COLUMN numero INTEGER NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Terrain AS SELECT id, tournoi_id, occupé, longueur, largeur FROM Terrain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Terrain
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Terrain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tournoi_id INTEGER DEFAULT NULL, occupé BOOLEAN NOT NULL, longueur INTEGER DEFAULT NULL, largeur INTEGER DEFAULT NULL, CONSTRAINT FK_7CB6A2DF607770A FOREIGN KEY (tournoi_id) REFERENCES Tournoi (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO Terrain (id, tournoi_id, occupé, longueur, largeur) SELECT id, tournoi_id, occupé, longueur, largeur FROM __temp__Terrain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__Terrain
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7CB6A2DF607770A ON Terrain (tournoi_id)
        SQL);
    }
}
