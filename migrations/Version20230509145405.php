<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509145405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post_notification DROP FOREIGN KEY FK_14690B194B89032C');
        $this->addSql('ALTER TABLE post_notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES shared_post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_notification DROP FOREIGN KEY FK_14690B194B89032C');
        $this->addSql('ALTER TABLE post_notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }
}
