<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609124227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutoriel ADD style_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoriel ADD CONSTRAINT FK_A2073AEDB591D0AC FOREIGN KEY (style_id_id) REFERENCES style (id)');
        $this->addSql('CREATE INDEX IDX_A2073AEDB591D0AC ON tutoriel (style_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutoriel DROP FOREIGN KEY FK_A2073AEDB591D0AC');
        $this->addSql('DROP INDEX IDX_A2073AEDB591D0AC ON tutoriel');
        $this->addSql('ALTER TABLE tutoriel DROP style_id_id');
    }
}
