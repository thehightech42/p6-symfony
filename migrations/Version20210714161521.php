<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714161521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD main_visuel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AB5EF48A3 FOREIGN KEY (main_visuel_id) REFERENCES visuel_figure (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F57B37AB5EF48A3 ON figure (main_visuel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AB5EF48A3');
        $this->addSql('DROP INDEX UNIQ_2F57B37AB5EF48A3 ON figure');
        $this->addSql('ALTER TABLE figure DROP main_visuel_id');
    }
}
