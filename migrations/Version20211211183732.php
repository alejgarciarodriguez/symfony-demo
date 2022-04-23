<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211211183732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE club (id VARCHAR(255) NOT NULL, budget DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE manager (id VARCHAR(255) NOT NULL, club_id VARCHAR(255) DEFAULT NULL, salary DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA2425B961190A32 ON manager (club_id)');
        $this->addSql('CREATE TABLE player (name VARCHAR(255) NOT NULL, club_id VARCHAR(255) DEFAULT NULL, salary DOUBLE PRECISION NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE INDEX IDX_98197A6561190A32 ON player (club_id)');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B961190A32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6561190A32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE manager DROP CONSTRAINT FK_FA2425B961190A32');
        $this->addSql('ALTER TABLE player DROP CONSTRAINT FK_98197A6561190A32');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE player');
    }
}
