<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221030112725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, content VARCHAR(255) DEFAULT NULL, is_publiched TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE painting ADD comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_66B9EBA0F8697D13 ON painting (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE painting DROP FOREIGN KEY FK_66B9EBA0F8697D13');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP INDEX IDX_66B9EBA0F8697D13 ON painting');
        $this->addSql('ALTER TABLE painting DROP comment_id');
    }
}
