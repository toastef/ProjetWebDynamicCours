<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221103131304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB8C43B94');
        $this->addSql('DROP INDEX IDX_9474526CB8C43B94 ON comment');
        $this->addSql('ALTER TABLE comment DROP paintings_id');
        $this->addSql('ALTER TABLE painting ADD comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_66B9EBA0F8697D13 ON painting (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD paintings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB8C43B94 FOREIGN KEY (paintings_id) REFERENCES painting (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9474526CB8C43B94 ON comment (paintings_id)');
        $this->addSql('ALTER TABLE painting DROP FOREIGN KEY FK_66B9EBA0F8697D13');
        $this->addSql('DROP INDEX IDX_66B9EBA0F8697D13 ON painting');
        $this->addSql('ALTER TABLE painting DROP comment_id');
    }
}
