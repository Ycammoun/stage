<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513072100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Match AS SELECT id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2 FROM "Match"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "Match"
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "Match" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, equipe1_id INTEGER NOT NULL, equipe2_id INTEGER NOT NULL, poule_id INTEGER NOT NULL, date_match DATE DEFAULT NULL, heur_debut TIME DEFAULT NULL, match_en_cours BOOLEAN NOT NULL, score1 INTEGER NOT NULL, score2 INTEGER NOT NULL, CONSTRAINT FK_BB9AEA014265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0150D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0126596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO "Match" (id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2) SELECT id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2 FROM __temp__Match
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__Match AS SELECT id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2 FROM "Match"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "Match"
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "Match" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, equipe1_id INTEGER NOT NULL, equipe2_id INTEGER NOT NULL, poule_id INTEGER NOT NULL, date_match DATE NOT NULL, heur_debut TIME NOT NULL, match_en_cours BOOLEAN NOT NULL, score1 INTEGER NOT NULL, score2 INTEGER NOT NULL, CONSTRAINT FK_BB9AEA014265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0150D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BB9AEA0126596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO "Match" (id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2) SELECT id, equipe1_id, equipe2_id, poule_id, date_match, heur_debut, match_en_cours, score1, score2 FROM __temp__Match
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
    }
}
