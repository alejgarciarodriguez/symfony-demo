<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220423172311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renames table "manager" to "referee"';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE referee (id VARCHAR(255) NOT NULL, club_id VARCHAR(255) DEFAULT NULL, salary DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D60FB34261190A32 ON referee (club_id)');
        $this->addSql('ALTER TABLE referee ADD CONSTRAINT FK_D60FB34261190A32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE manager');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE manager (id VARCHAR(255) NOT NULL, club_id VARCHAR(255) DEFAULT NULL, salary DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fa2425b961190a32 ON manager (club_id)');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT fk_fa2425b961190a32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE referee');
    }
}
