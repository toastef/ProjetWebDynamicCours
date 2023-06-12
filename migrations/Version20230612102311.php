<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612102311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, painting_id INT DEFAULT NULL, user_id INT DEFAULT NULL, content VARCHAR(255) DEFAULT NULL, is_publiched TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CB00EB939 (painting_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, paintlike_id INT DEFAULT NULL, INDEX IDX_AC6340B3A76ED395 (user_id), INDEX IDX_AC6340B3477BC1C9 (paintlike_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE painting (id INT AUTO_INCREMENT NOT NULL, style_id INT NOT NULL, categories_id INT NOT NULL, vendeur_id INT NOT NULL, title VARCHAR(120) NOT NULL, descrioption VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', height DOUBLE PRECISION NOT NULL, width DOUBLE PRECISION NOT NULL, image_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', price INT NOT NULL, selected TINYINT(1) NOT NULL, vendu TINYINT(1) NOT NULL, INDEX IDX_66B9EBA0BACD6074 (style_id), INDEX IDX_66B9EBA0A21214B7 (categories_id), INDEX IDX_66B9EBA0858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE style (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuto_comment (id INT AUTO_INCREMENT NOT NULL, tutorial_id INT NOT NULL, user_id INT NOT NULL, content VARCHAR(255) NOT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2A8A85BA89366B7B (tutorial_id), INDEX IDX_2A8A85BAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutoriel (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, style_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_A2073AED12469DE2 (category_id), INDEX IDX_A2073AEDBACD6074 (style_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tutoriel (user_id INT NOT NULL, tutoriel_id INT NOT NULL, INDEX IDX_428ADEEDA76ED395 (user_id), INDEX IDX_428ADEED7CB6CDBB (tutoriel_id), PRIMARY KEY(user_id, tutoriel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB00EB939 FOREIGN KEY (painting_id) REFERENCES painting (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3477BC1C9 FOREIGN KEY (paintlike_id) REFERENCES painting (id)');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0BACD6074 FOREIGN KEY (style_id) REFERENCES style (id)');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0A21214B7 FOREIGN KEY (categories_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0858C065E FOREIGN KEY (vendeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tuto_comment ADD CONSTRAINT FK_2A8A85BA89366B7B FOREIGN KEY (tutorial_id) REFERENCES tutoriel (id)');
        $this->addSql('ALTER TABLE tuto_comment ADD CONSTRAINT FK_2A8A85BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tutoriel ADD CONSTRAINT FK_A2073AED12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE tutoriel ADD CONSTRAINT FK_A2073AEDBACD6074 FOREIGN KEY (style_id) REFERENCES style (id)');
        $this->addSql('ALTER TABLE user_tutoriel ADD CONSTRAINT FK_428ADEEDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tutoriel ADD CONSTRAINT FK_428ADEED7CB6CDBB FOREIGN KEY (tutoriel_id) REFERENCES tutoriel (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB00EB939');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3477BC1C9');
        $this->addSql('ALTER TABLE painting DROP FOREIGN KEY FK_66B9EBA0BACD6074');
        $this->addSql('ALTER TABLE painting DROP FOREIGN KEY FK_66B9EBA0A21214B7');
        $this->addSql('ALTER TABLE painting DROP FOREIGN KEY FK_66B9EBA0858C065E');
        $this->addSql('ALTER TABLE tuto_comment DROP FOREIGN KEY FK_2A8A85BA89366B7B');
        $this->addSql('ALTER TABLE tuto_comment DROP FOREIGN KEY FK_2A8A85BAA76ED395');
        $this->addSql('ALTER TABLE tutoriel DROP FOREIGN KEY FK_A2073AED12469DE2');
        $this->addSql('ALTER TABLE tutoriel DROP FOREIGN KEY FK_A2073AEDBACD6074');
        $this->addSql('ALTER TABLE user_tutoriel DROP FOREIGN KEY FK_428ADEEDA76ED395');
        $this->addSql('ALTER TABLE user_tutoriel DROP FOREIGN KEY FK_428ADEED7CB6CDBB');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE painting');
        $this->addSql('DROP TABLE style');
        $this->addSql('DROP TABLE tuto_comment');
        $this->addSql('DROP TABLE tutoriel');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_tutoriel');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
