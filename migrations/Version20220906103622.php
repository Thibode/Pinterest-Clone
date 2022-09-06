<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906103622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM pins');
        $this->addSql('ALTER TABLE pins ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE pins ADD CONSTRAINT FK_3F0FE9807E3C61F9 FOREIGN KEY (owner_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3F0FE9807E3C61F9 ON pins (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pins DROP CONSTRAINT FK_3F0FE9807E3C61F9');
        $this->addSql('DROP INDEX IDX_3F0FE9807E3C61F9');
        $this->addSql('ALTER TABLE pins DROP owner_id');
    }
}
