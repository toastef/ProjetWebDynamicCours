<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106131714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slider (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slider_painting (slider_id INT NOT NULL, painting_id INT NOT NULL, INDEX IDX_B9A7FF462CCC9638 (slider_id), INDEX IDX_B9A7FF46B00EB939 (painting_id), PRIMARY KEY(slider_id, painting_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE slider_painting ADD CONSTRAINT FK_B9A7FF462CCC9638 FOREIGN KEY (slider_id) REFERENCES slider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE slider_painting ADD CONSTRAINT FK_B9A7FF46B00EB939 FOREIGN KEY (painting_id) REFERENCES painting (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slider_painting DROP FOREIGN KEY FK_B9A7FF462CCC9638');
        $this->addSql('ALTER TABLE slider_painting DROP FOREIGN KEY FK_B9A7FF46B00EB939');
        $this->addSql('DROP TABLE slider');
        $this->addSql('DROP TABLE slider_painting');
    }
}
