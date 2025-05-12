<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512090305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE Tournoi ADD COLUMN nombre_terrain INTEGER DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Tournoi AS SELECT id, date_tournoi, intitule FROM Tournoi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Tournoi
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Tournoi (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_tournoi DATE NOT NULL, intitule VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO Tournoi (id, date_tournoi, intitule) SELECT id, date_tournoi, intitule FROM __temp__Tournoi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__Tournoi
        SQL);
    }
}
