<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210916221358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visuel_figure DROP FOREIGN KEY FK_D8633F45C011B5');
        $this->addSql('ALTER TABLE visuel_figure ADD CONSTRAINT FK_D8633F45C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visuel_figure DROP FOREIGN KEY FK_D8633F45C011B5');
        $this->addSql('ALTER TABLE visuel_figure ADD CONSTRAINT FK_D8633F45C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
    }
}
