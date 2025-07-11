<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250619132447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE "Match" ADD COLUMN bracket BOOLEAN DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Match AS SELECT id, equipe1_id, equipe2_id, poule_id, terrain_id, date_match, heur_debut, match_en_cours, score1, score2, set_par_equipe, valide FROM "Match"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "Match"
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "Match" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, equipe1_id INTEGER NOT NULL, equipe2_id INTEGER NOT NULL, poule_id INTEGER NOT NULL, terrain_id INTEGER DEFAULT NULL, date_match DATE DEFAULT NULL, heur_debut TIME DEFAULT NULL, match_en_cours BOOLEAN NOT NULL, score1 INTEGER NOT NULL, score2 INTEGER NOT NULL, set_par_equipe INTEGER DEFAULT NULL, valide BOOLEAN DEFAULT NULL, CONSTRAINT FK_BB9AEA014265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0150D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0126596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA018A2D8B41 FOREIGN KEY (terrain_id) REFERENCES Terrain (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO "Match" (id, equipe1_id, equipe2_id, poule_id, terrain_id, date_match, heur_debut, match_en_cours, score1, score2, set_par_equipe, valide) SELECT id, equipe1_id, equipe2_id, poule_id, terrain_id, date_match, heur_debut, match_en_cours, score1, score2, set_par_equipe, valide FROM __temp__Match
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__Match
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
            CREATE INDEX IDX_BB9AEA018A2D8B41 ON "Match" (terrain_id)
        SQL);
    }
}
